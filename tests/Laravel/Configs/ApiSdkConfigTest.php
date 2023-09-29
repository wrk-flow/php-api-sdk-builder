<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel\Configs;

use Closure;
use WrkFlow\ApiSdkBuilder\Laravel\Configs\ApiSdkConfig;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggersMapEntity;
use WrkFlow\ApiSdkBuilderTests\Laravel\TestCase;

class ApiSdkConfigTest extends TestCase
{
    /**
     * @return array<string|int, array{0: Closure(static,ApiSdkConfig):void}>
     */
    public function data(): array
    {
        return [
            'getLoggers' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    expected: new LoggersMapEntity(LoggerConstants::DefaultLoggersMap),
                    actual: $config->getLoggers(),
                ),
            ],
            'getLogging' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    expected: '*:info_file',
                    actual: $config->getLogging(),
                ),
            ],
            'isTelescopeEnabled' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    expected: true,
                    actual: $config->isTelescopeEnabled(),
                ),
            ],
            'getTimeForClearSchedule' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    expected: '00:10',
                    actual: $config->getTimeForClearSchedule(),
                ),
            ],
            'getLogFileBaseDirectory' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    expected: 'requests',
                    actual: $config->getLogFileBaseDirectory(),
                ),
            ],
            'getKeepLogFilesForDays' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    expected: 14,
                    actual: $config->getKeepLogFilesForDays(),
                ),
            ],
        ];
    }


    /**
     * @param Closure(static,ApiSdkConfig):void $assert
     * @dataProvider data
     */
    public function test(Closure $assert): void
    {
        $config = $this->app()
            ->make(ApiSdkConfig::class);
        assert($config instanceof ApiSdkConfig);
        $assert($this, $config);
    }
}
