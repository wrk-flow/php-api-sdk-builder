<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan;

use LaraStrict\Conventions\PHPStan\ClassPlacementRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use WrkFlow\ApiSdkBuilder\PHPStan\SdkClassPlacementExtension;

/**
 * @extends RuleTestCase<ClassPlacementRule>
 */
final class SdkClassPlacementExtensionTest extends RuleTestCase
{
    public function testEndpointEntitiesAndSharedEntitiesAreAllowed(): void
    {
        $this->analyse([
            __DIR__ . '/Fixtures/Confirme/Sdk/Endpoints/Verifiers/DeviceEntity.php',
            __DIR__ . '/Fixtures/Confirme/Api/Endpoints/Verifiers/Nested/DeviceEntity.php',
            __DIR__ . '/Fixtures/Confirme/Entities/DeviceEntity.php',
        ], []);
    }

    public function testEndpointFolderIsRequiredAndOtherSuffixesKeepTheirRules(): void
    {
        $this->analyse([
            __DIR__ . '/Fixtures/Confirme/Sdk/Endpoints/DeviceEntity.php',
            __DIR__ . '/Fixtures/Confirme/Sdk/Endpoints/Verifiers/DeviceService.php',
        ], [
            [
                'Class DeviceEntity must be placed in Entities or Endpoints\\* or Http\\<area>\\<endpoint> because its name ends with "Entity".',
                7,
            ],
            ['Class DeviceService must be placed in Services because its name ends with "Service".', 7],
        ]);
    }
    protected function getRule(): Rule
    {
        return new ClassPlacementRule(extensions: [new SdkClassPlacementExtension()]);
    }
}
