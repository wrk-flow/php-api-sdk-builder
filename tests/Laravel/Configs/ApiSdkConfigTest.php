<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel\Configs;

use Closure;
use Illuminate\Config\Repository;
use InvalidArgumentException;
use stdClass;
use WrkFlow\ApiSdkBuilder\Laravel\Configs\ApiSdkConfig;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggersMapEntity;
use WrkFlow\ApiSdkBuilderTests\Laravel\TestCase;
use Wrkflow\GetValue\GetValueFactory;

class ApiSdkConfigTest extends TestCase
{
    public function testInvalidLoggerClassIsRejected(): void
    {
        $config = new ApiSdkConfig(new Repository([
            'api_sdk' => [
                'logging' => [
                    'loggers' => [
                        'invalid' => [stdClass::class],
                    ],
                ],
            ],
        ]), new GetValueFactory());
        $this->expectException(InvalidArgumentException::class);
        $config->getLoggers();
    }

    public function testInvalidLoggerMapIsRejected(): void
    {
        $config = new ApiSdkConfig(new Repository([
            'api_sdk' => [
                'logging' => [
                    'loggers' => [
                        'invalid' => 'logger',
                    ],
                ],
            ],
        ]), new GetValueFactory());
        $this->expectException(InvalidArgumentException::class);
        $config->getLoggers();
    }

    /**
     * @return array<string|int, array{0: Closure(static,ApiSdkConfig):void}>
     */
    public static function data(): array
    {
        return [
            'getLoggers' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    new LoggersMapEntity(LoggerConstants::DefaultLoggersMap),
                    $config->getLoggers(),
                ),
            ],
            'getLogging' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    '*:info_file',
                    $config->getLogging(),
                ),
            ],
            'isTelescopeEnabled' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    true,
                    $config->isTelescopeEnabled(),
                ),
            ],
            'getTimeForClearSchedule' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    '00:10',
                    $config->getTimeForClearSchedule(),
                ),
            ],
            'getLogFileBaseDirectory' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    'requests',
                    $config->getLogFileBaseDirectory(),
                ),
            ],
            'getKeepLogFilesForDays' => [
                static fn (self $self, ApiSdkConfig $config) => $self->assertEquals(
                    14,
                    $config->getKeepLogFilesForDays(),
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
