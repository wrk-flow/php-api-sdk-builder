<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use Illuminate\Console\Scheduling\Schedule;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

class LaravelServiceProviderTest extends TestCase
{
    public function testLoggerCanBeBuilt(): void
    {
        foreach (LoggerConstants::DefaultLoggersMap as $loggers) {
            foreach ($loggers as $logger) {
                $this->assertInstanceOf(expected: ApiLoggerInterface::class, actual: $this->app()->make($logger));
            }
        }
    }

    public function testClearFilesLogsCommandRegistered(): void
    {
        $schedule = $this->app()
            ->make(Schedule::class);
        assert($schedule instanceof Schedule);

        $commands = [
            'api-sdk:logs:clear' => false,
        ];

        foreach ($schedule->events() as $event) {
            if ($event->command === null) {
                continue;
            }

            foreach (array_keys($commands) as $command) {
                if (str_contains($event->command, $command)) {
                    $commands[$command] = true;
                }
            }
        }

        foreach ($commands as $command => $found) {
            $this->assertTrue($found, sprintf('Command %s not found in schedule', $command));
        }
    }
}
