<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Http;

use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Http\ResponseBufferMiddleware;
use WrkFlow\ApiSdkBuilder\Http\TemporaryResponseStorage;

final class TemporaryResponseStorageTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir() . '/api-sdk-buffer-test-' . bin2hex(random_bytes(8));
    }

    protected function tearDown(): void
    {
        if (is_dir($this->directory)) {
            foreach (glob($this->directory . '/*') ?: [] as $file) {
                unlink($file);
            }

            rmdir($this->directory);
        }

        parent::tearDown();
    }

    public function testSmallResponsesStayInMemoryAndLargeResponsesReleaseTheDiskFile(): void
    {
        $storage = new TemporaryResponseStorage($this->directory);
        $middleware = new ResponseBufferMiddleware($storage);
        $payload = 'small response';
        $handler = $middleware(static function (RequestInterface $request, array $options) use (&$payload): PromiseInterface {
            $stream = $options['sink'];
            self::assertInstanceOf(StreamInterface::class, $stream);
            $stream->write(substr($payload, 0, 100));
            $stream->write(substr($payload, 100));
            $stream->rewind();

            return Create::promiseFor(new Response(200, [], $stream));
        });
        $request = new Request('GET', 'https://example.test');

        $response = $handler($request, [])->wait();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame($payload, $response->getBody()->getContents());
        self::assertDirectoryDoesNotExist($this->directory);

        $payload = str_repeat('x', 3 * 1024 * 1024);
        $response = $handler($request, [])->wait();
        self::assertInstanceOf(Response::class, $response);
        self::assertSame($payload, $response->getBody()->getContents());
        $files = glob($this->directory . '/*');
        self::assertIsArray($files);
        self::assertCount(1, $files);
        self::assertTrue(touch($files[0], time() - 172800));
        self::assertSame(0, $storage->clearExpired(time() - 86400));

        $response->getBody()->close();
        self::assertFileDoesNotExist($files[0]);
    }

    public function testCleanupRemovesOldAbandonedBuffersOnly(): void
    {
        self::assertTrue(mkdir($this->directory, 0700));
        $old = $this->directory . '/api-sdk-response-orphan';
        $new = $this->directory . '/api-sdk-response-recent';
        $other = $this->directory . '/other-tmp-file';
        foreach ([$old, $new, $other] as $file) {
            file_put_contents($file, 'data');
        }

        foreach ([$old, $other] as $file) {
            self::assertTrue(touch($file, time() - 172800));
        }

        self::assertSame(1, (new TemporaryResponseStorage($this->directory))->clearExpired(time() - 86400));
        self::assertFileDoesNotExist($old);
        self::assertFileExists($new);
        self::assertFileExists($other);
    }

    public function testDroppingTheLastReferenceClosesAndRemovesTheBuffer(): void
    {
        $stream = (new TemporaryResponseStorage($this->directory))->createFile();
        $path = $stream->getMetadata('uri');
        self::assertIsString($path);

        $response = new Response(200, [], $stream);
        unset($stream);
        self::assertFileExists($path);

        $body = $response->getBody();
        unset($response);
        self::assertFileExists($path);

        unset($body);
        self::assertFileDoesNotExist($path);
    }

    public function testExplicitSinkAndUnbufferedRequestsArePreserved(): void
    {
        $middleware = new ResponseBufferMiddleware(new TemporaryResponseStorage($this->directory));
        $sink = Utils::streamFor();
        $handler = $middleware(static function (RequestInterface $request, array $options) use ($sink): PromiseInterface {
            if (($options['stream'] ?? false) === true) {
                self::assertArrayNotHasKey('sink', $options);
            } else {
                self::assertSame($sink, $options['sink']);
            }

            return Create::promiseFor(new Response());
        });
        $request = new Request('GET', 'https://example.test');
        $handler($request, [
            'sink' => $sink,
        ])->wait();
        $handler($request, [
            'stream' => true,
        ])->wait();
    }
}
