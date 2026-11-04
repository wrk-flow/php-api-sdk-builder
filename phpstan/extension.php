<?php

declare(strict_types=1);

use LaraStrict\Conventions\PHPStan\ClassPlacementExtension;

if (! interface_exists(ClassPlacementExtension::class)) {
    return [];
}

return [
    'includes' => [__DIR__ . '/extension.neon'],
];
