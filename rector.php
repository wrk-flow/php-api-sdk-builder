<?php declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use StrictPhp\Conventions\ExtensionFiles;

return RectorConfig::configure()
    ->withRootFiles()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/phpstan/src', __DIR__ . '/phpstan/tests'])
    ->withSets([ExtensionFiles::Rector])
    ->withPhpVersion(PhpVersion::PHP_81)
    ->withSkip([__DIR__ . '/phpstan/tests/Fixtures']);
