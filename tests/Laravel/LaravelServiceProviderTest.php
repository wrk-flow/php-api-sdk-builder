<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use Illuminate\Console\Scheduling\Schedule;
use WrkFlow\ApiSdkBuilder\Log\Actions\ClearFileLogsAction;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggersMapEntity;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;
use WrkFlow\ApiSdkBuilder\Log\Loggers\FileLogger;

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

    public function testLoggerConfigEntity(): void
    {
        $logger = $this->make(LoggerConfigEntity::class);

        $this->assertEquals(
            expected: new LoggerConfigEntity(
                logger: '*:info_file',
                loggersMap: new LoggersMapEntity(LoggerConstants::DefaultLoggersMap),
                fileBaseDir: 'requests',
                keepLogFilesForDays: 14,
            ),
            actual: $logger,
        );
    }

    public function testFilesystemOperatorIsInjectable(): void
    {
        $this->assertInstanceOf(expected: FileLogger::class, actual: $this->app() ->make(FileLogger::class));
        $this->assertInstanceOf(
            expected: ClearFileLogsAction::class,
            actual: $this->app()
                ->make(ClearFileLogsAction::class),
        );
    }
}
