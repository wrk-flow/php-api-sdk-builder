<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel\Configs;

use Closure;
use Illuminate\Config\Repository;
use WrkFlow\ApiSdkBuilder\Laravel\Configs\ApiSdkConfig;
use WrkFlow\ApiSdkBuilderTests\Laravel\TestCase;
use Wrkflow\GetValue\GetValueFactory;

class ApiSdkConfigLoggingTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static):void}>
     */
    public function data(): array
    {
        return [
            'non empty string' => [
                static fn (self $self) => $self->assertConfig(set: 'info', expected: 'info'),
            ],
            'empty string' => [
                static fn (self $self) => $self->assertConfig(set: '', expected: ''),
            ],
            'null' => [
                static fn (self $self) => $self->assertConfig(set: null, expected: ''),
            ],
        ];
    }


    /**
     * @param Closure(static):void $assert
     *
     * @dataProvider data
     */
    public function test(Closure $assert): void
    {
        $assert($this);
    }

    private function assertConfig(?string $set, string $expected): void
    {
        $config = new ApiSdkConfig(
            config: new Repository([
                'api_sdk' => [
                    ApiSdkConfig::KeyLogging => [
                        ApiSdkConfig::KeyLoggingType => $set,
                    ],
                ],
            ]),
            getValueFactory: new GetValueFactory()
        );
        $this->assertEquals($config->getLogging(), $expected);
    }
}
