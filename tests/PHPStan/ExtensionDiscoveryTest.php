<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ExtensionDiscoveryTest extends TestCase
{
    public function testConfigurationIsSafeWithoutLarastrict(): void
    {
        $process = new Process([
            PHP_BINARY,
            '-r',
            'echo json_encode(require $argv[1]);',
            dirname(__DIR__, 2) . '/phpstan/extension.php',
        ]);
        $process->setTimeout(10);
        $process->run();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertSame('[]', $process->getOutput());
    }

    public function testOlderConventionsKeepPlacementWithoutUnsupportedInjectionSettings(): void
    {
        $script = <<<'PHP_WRAP'
namespace LaraStrict\Conventions\PHPStan {
    interface ClassPlacementExtension {}
    class ParameterNameRule { public function __construct() {} }
}
namespace { echo json_encode(require $argv[1]); }
PHP_WRAP;
        $process = new Process([PHP_BINARY, '-r', $script, dirname(__DIR__, 2) . '/phpstan/extension.php']);
        $process->setTimeout(10);
        $process->run();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertSame([
            'includes' => [dirname(__DIR__, 2) . '/phpstan/extension.neon'],
        ], json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR));
    }
}
