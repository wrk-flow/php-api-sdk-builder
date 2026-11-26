<?php declare(strict_types=1);

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Actions\MakeBodyFromResponseAction;
use WrkFlow\ApiSdkBuilder\Http\ResponseBufferMiddleware;
use WrkFlow\ApiSdkBuilder\Http\Services\TemporaryResponseStorageService;
use WrkFlow\ApiSdkBuilder\Responses\AbstractXMLResponse;
use WrkFlow\ApiSdkBuilder\Responses\StreamedXmlBody;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\Transformers\ArrayItemGetterTransformer;

/**
 * @param list<string> $arguments
 * @param resource $output
 */
function runBenchmarkProcess(array $arguments, $output): int
{
    // Peak memory is a process lifetime metric. Use a fresh PHP process for each mode.
    $process = proc_open([PHP_BINARY, __FILE__, ...$arguments], [STDIN, $output, STDERR], $pipes);

    if ($process === false) {
        fwrite(STDERR, "Cannot start the benchmark process.\n");

        return 1;
    }

    return proc_close($process);
}

$parseModes = ['legacy', 'streamed', 'buffered'];
$defaultAutoload = __DIR__ . '/../vendor/autoload.php';
$mode = $argv[1] ?? '';
$source = $argv[2] ?? '';

if (! in_array($mode, [...$parseModes, 'generate', 'compare', 'compare-sample'], true) || ! is_file($source) || ! is_readable($source)) {
    fwrite(STDERR, "Usage: php benchmark-rate-plan-xml.php compare RESPONSE.xml [AUTOLOAD.php] [MODE]\n");
    fwrite(STDERR, "       php benchmark-rate-plan-xml.php compare-sample SAMPLE.xml [SIZE_MIB] [AUTOLOAD.php] [MODE]\n");
    fwrite(STDERR, "       php benchmark-rate-plan-xml.php legacy|streamed|buffered RESPONSE.xml [AUTOLOAD.php]\n");
    fwrite(STDERR, "       php benchmark-rate-plan-xml.php generate SAMPLE.xml [SIZE_MIB] > TEMPFILE\n");
    exit($mode === '--help' ? 0 : 2);
}

if ($mode === 'compare' || $mode === 'compare-sample') {
    $synthetic = $mode === 'compare-sample';
    $autoload = $argv[$synthetic ? 4 : 3] ?? $defaultAutoload;
    $selectedMode = $argv[$synthetic ? 5 : 4] ?? '';

    if ($selectedMode !== '' && ! in_array($selectedMode, $parseModes, true)) {
        fwrite(STDERR, "MODE must be legacy, streamed or buffered. Omit it to run all three modes.\n");
        exit(2);
    }

    if ($synthetic) {
        $temporaryResponse = tmpfile();

        if ($temporaryResponse === false) {
            fwrite(STDERR, "Cannot create a temporary response file.\n");
            exit(1);
        }

        $status = runBenchmarkProcess(['generate', $source, $argv[3] ?? '4'], $temporaryResponse);

        if ($status !== 0) {
            exit($status);
        }

        $source = stream_get_meta_data($temporaryResponse)['uri'];
    }

    foreach ($selectedMode === '' ? $parseModes : [$selectedMode] as $parseMode) {
        $status = runBenchmarkProcess([$parseMode, $source, $autoload], STDOUT);

        if ($status !== 0) {
            exit($status);
        }
    }

    exit(0);
}

if ($mode === 'generate') {
    $size = filter_var($argv[3] ?? '4', FILTER_VALIDATE_INT, [
        'options' => [
            'min_range' => 1,
            'max_range' => 1024,
        ],
    ]);
    $sample = simplexml_load_file($source, SimpleXMLElement::class, LIBXML_NONET);
    $plan = $sample instanceof SimpleXMLElement ? $sample->RatePlans->RatePlan[0] : null;
    $planXml = $plan instanceof SimpleXMLElement ? $plan->asXML() : false;
    $rateXml = $plan instanceof SimpleXMLElement ? $plan->Rates->Rate[0]?->asXML() : false;

    if ($size === false || ! is_string($planXml) || ! is_string($rateXml)
        || ! str_contains($planXml, '<Rates>') || ! str_contains($planXml, '</Rates>')) {
        fwrite(STDERR, "Supply a sample with a RatePlans/RatePlan/Rates/Rate element and a size from 1 to 1024 MiB.\n");
        exit(2);
    }

    [$prefix, $ratesAndSuffix] = explode('<Rates>', $planXml, 2);
    [, $suffix] = explode('</Rates>', $ratesAndSuffix, 2);
    $planCount = 160;
    $planOverhead = strlen($prefix . '<Rates></Rates>' . $suffix);
    $ratesPerPlan = max(1, (int) ceil(($size * 1048576 / $planCount - $planOverhead) / strlen($rateXml)));
    $rates = str_repeat($rateXml, $ratesPerPlan);

    echo '<?xml version="1.0" encoding="utf-8"?>', PHP_EOL;
    echo '<OTA_HotelRatePlanRS xmlns="http://www.opentravel.org/OTA/2003/05"><Success/><RatePlans>';
    for ($index = 0; $index < $planCount; $index++) {
        echo $prefix, '<Rates>', $rates, '</Rates>', $suffix;
    }

    echo '</RatePlans></OTA_HotelRatePlanRS>', PHP_EOL;
    fprintf(STDERR, "Synthetic response with %d plans and %d repeated rates per plan.\n", $planCount, $ratesPerPlan);
    exit(0);
}

$autoload = $argv[3] ?? $defaultAutoload;

if (! is_file($autoload)) {
    fwrite(STDERR, "Pass the Composer autoloader as the third argument.\n");
    exit(2);
}

require $autoload;

$input = Utils::streamFor(Utils::tryFopen($source, 'rb'));
$fileSize = $input->getSize();
$bufferSeconds = null;
$bufferPath = null;

if ($mode === 'buffered') {
    $directory = sys_get_temp_dir() . '/api-sdk-rate-plan-bench-' . bin2hex(random_bytes(8));
    register_shutdown_function(static function () use ($directory): void {
        if (is_dir($directory)) {
            foreach (glob($directory . '/*') ?: [] as $file) {
                unlink($file);
            }

            rmdir($directory);
        }
    });

    $middleware = new ResponseBufferMiddleware(new TemporaryResponseStorageService($directory));
    $handler = $middleware(static function (RequestInterface $request, array $options) use ($input): PromiseInterface {
        $sink = $options['sink'];
        Utils::copyToStream($input, $sink);
        $sink->rewind();

        return Create::promiseFor(new Response(200, [], $sink));
    });
    $request = ResponseBufferMiddleware::forceFor(new Request('GET', 'https://example.test'));
    $bufferStarted = hrtime(true);
    $response = $handler($request, [])->wait();
    $bufferSeconds = round((hrtime(true) - $bufferStarted) / 1e9, 3);
    $uri = $response->getBody()->getMetadata('uri');

    if (is_string($uri) && dirname($uri) === $directory && is_file($uri)) {
        $bufferPath = $uri;
    }

    if ($fileSize > 2 * 1024 * 1024 && $bufferPath === null) {
        fwrite(STDERR, "Expected the response to spill to disk.\n");
        exit(1);
    }
} else {
    $response = new Response(200, [], $input);
}

$count = 0;
$rates = 0;
$hash = hash_init('sha256');
$visit = static function (GetValue $plan) use (&$count, &$rates, $hash): bool {
    $count++;
    $planRates = $plan->getNullableXML('Rates');

    if ($planRates instanceof SimpleXMLElement) {
        $rates += count($planRates->Rate);
    }

    hash_update($hash, ($plan->getXMLAttributesGetter()->getString('RatePlanCode') ?? '') . "\n");

    return true;
};

$started = hrtime(true);

if ($mode === 'legacy') {
    $body = (new MakeBodyFromResponseAction())->execute(AbstractXMLResponse::class, $response);
    $header = $body;
} else {
    $body = new StreamedXmlBody($response);
    $header = $body->readHeader(['Success', 'Errors']);
}

if (! $header instanceof GetValue || ! $header->getNullableXML('Success') instanceof SimpleXMLElement) {
    fwrite(STDERR, "The XML does not contain a successful rate plan response.\n");
    exit(2);
}

$validated = hrtime(true);

if ($body instanceof StreamedXmlBody) {
    foreach ($body->elements(['RatePlans', 'RatePlan']) as $plan) {
        $visit($plan);
    }
} elseif ($body instanceof GetValue) {
    $body->getArray(['RatePlans', 'RatePlan'], [new ArrayItemGetterTransformer($visit)]);
}

$finished = hrtime(true);
unset($body, $response);
$cleaned = $bufferPath === null ? null : ! file_exists($bufferPath);

if ($cleaned === false) {
    fwrite(STDERR, "The response buffer still exists after releasing the response.\n");
    exit(1);
}

$usage = getrusage();
$rssDivisor = PHP_OS_FAMILY === 'Darwin' ? 1048576 : 1024;
echo json_encode([
    'mode' => $mode,
    'php' => PHP_VERSION,
    'file_mib' => round($fileSize / 1048576, 2),
    'rate_plans' => $count,
    'rates' => $rates,
    'rate_plan_code_digest' => hash_final($hash),
    'seconds' => round(($finished - $started) / 1e9, 3),
    'load_seconds' => round(($validated - $started) / 1e9, 3),
    'items_seconds' => round(($finished - $validated) / 1e9, 3),
    'buffer_seconds' => $bufferSeconds,
    'spilled_to_disk' => $mode === 'buffered' ? $bufferPath !== null : null,
    'spill_removed' => $cleaned,
    'php_peak_mib' => round(memory_get_peak_usage(true) / 1048576, 2),
    'peak_rss_mib' => $usage === false ? null : round($usage['ru_maxrss'] / $rssDivisor, 2),
], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR), PHP_EOL;
