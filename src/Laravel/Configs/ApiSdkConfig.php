<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel\Configs;

use InvalidArgumentException;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggersMapEntity;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

class ApiSdkConfig extends AbstractConfig
{
    final public const KeyLogging = 'logging';
    final public const KeyLoggingType = 'type';
    final public const KeyLoggers = 'loggers';
    final public const KeyTimeForClearLogSchedule = 'clear_schedule_time';
    final public const KeyLogFileBaseDirectory = 'logs_file_base_directory';
    final public const KeyKeepLogFilesForDays = 'keep_log_files_for_days';
    final public const KeyIsTelescopeEnabled = 'is_telescope_enabled';

    public function getLoggers(): LoggersMapEntity
    {
        $loggers = [];
        foreach ($this->config->getArray([self::KeyLogging, self::KeyLoggers]) as $name => $classes) {
            if (! is_string($name) || ! is_array($classes)) {
                throw new InvalidArgumentException('Logger configuration must map string names to arrays of logger classes.');
            }

            $loggers[$name] = [];
            foreach ($classes as $class) {
                if (! is_string($class) || ! is_a($class, ApiLoggerInterface::class, true)) {
                    throw new InvalidArgumentException('Configured logger must implement ' . ApiLoggerInterface::class);
                }

                $loggers[$name][] = $class;
            }
        }

        return new LoggersMapEntity($loggers);
    }

    public function getLogging(): string
    {
        return $this->config->getString([self::KeyLogging, self::KeyLoggingType]) ?? '';
    }

    public function isTelescopeEnabled(): bool
    {
        return $this->config->getRequiredBool(self::KeyIsTelescopeEnabled);
    }

    public function getTimeForClearSchedule(): ?string
    {
        return $this->config->getString([self::KeyLogging, self::KeyTimeForClearLogSchedule]);
    }

    public function getLogFileBaseDirectory(): string
    {
        return $this->config->getRequiredString([self::KeyLogging, self::KeyLogFileBaseDirectory]);
    }

    public function getKeepLogFilesForDays(): int
    {
        return $this->config->getRequiredInt([self::KeyLogging, self::KeyKeepLogFilesForDays]);
    }

    protected function getConfigFileName(): string
    {
        return 'api_sdk';
    }
}
