<?php

declare(strict_types=1);

use LaraStrict\Conventions\PHPStan\ClassPlacementExtension;
use LaraStrict\Conventions\PHPStan\ParameterNameRule;

if (! interface_exists(ClassPlacementExtension::class)) {
    return [];
}

$includes = [__DIR__ . '/extension.neon'];

if (class_exists(ParameterNameRule::class)) {
    foreach ((new ReflectionMethod(ParameterNameRule::class, '__construct'))->getParameters() as $parameter) {
        if ($parameter->getName() === 'additionalComponentNamespaces') {
            $includes[] = __DIR__ . '/injection.neon';

            break;
        }
    }
}

return [
    'includes' => $includes,
];
