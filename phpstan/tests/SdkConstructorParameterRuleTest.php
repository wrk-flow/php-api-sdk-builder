<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan;

use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use WrkFlow\PHPstan\ApiSdkBuilder\SdkConstructorParameterRule;

/**
 * @extends RuleTestCase<SdkConstructorParameterRule>
 */
final class SdkConstructorParameterRuleTest extends RuleTestCase
{
    public function testRuntimeArgumentsKeepTheNamesUsedByTheSdkFactory(): void
    {
        $this->analyse([__DIR__ . '/Fixtures/Constructors/BadResponse.php'], [
            [
                'SDK constructor parameter $reply must be named $response; the SDK factory supplies this argument by name.',
                14,
            ],
            [
                'SDK constructor parameter $payload must be named $body; the SDK factory supplies this argument by name.',
                14,
            ],
            [
                'SDK constructor parameter $factory must be named $container; the SDK factory supplies this argument by name.',
                14,
            ],
        ]);
        $this->analyse([__DIR__ . '/Fixtures/Constructors/BadEndpoint.php'], [
            [
                'SDK constructor parameter $dependencies must be named $di; the SDK factory supplies this argument by name.',
                12,
            ],
        ]);
    }

    public function testNamedRuntimeInputsMayBeReorderedAndOtherInputsKeepTheirNames(): void
    {
        $this->analyse([
            __DIR__ . '/Fixtures/Constructors/GoodResponse.php',
            __DIR__ . '/Fixtures/Constructors/OrdinaryInput.php',
        ], []);
    }

    protected function getRule(): Rule
    {
        return new SdkConstructorParameterRule();
    }
}
