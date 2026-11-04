<?php

declare(strict_types=1);

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
}
