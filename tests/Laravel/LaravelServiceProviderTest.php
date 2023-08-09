<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Contracts\LoggerContract;

class LaravelServiceProviderTest extends TestCase
{
    public function testLoggerCanBeBuilt(): void
    {
        foreach (LoggerConstants::DefaultLoggersMap as $loggers) {
            foreach ($loggers as $logger) {
                $this->assertInstanceOf(expected: LoggerContract::class, actual: $this->app()->make($logger));
            }
        }
    }
}
