<?php declare(strict_types=1);

namespace WrkFlow\PHPstan\ApiSdkBuilder;

use LaraStrict\Conventions\PHPStan\ClassPlacementExtension;

final class SdkClassPlacementExtension implements ClassPlacementExtension
{
    public function getAllowedNamespacePaths(string $suffix): array
    {
        return $suffix === 'Entity' ? [['Endpoints', '*']] : [];
    }
}
