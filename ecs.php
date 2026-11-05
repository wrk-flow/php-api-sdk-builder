<?php declare(strict_types=1);

use StrictPhp\Conventions\ExtensionFiles;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withRootFiles()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/phpstan/src', __DIR__ . '/phpstan/tests'])
    ->withSets([ExtensionFiles::Ecs])
    ->withSkip([__DIR__ . '/phpstan/tests/Fixtures']);
