<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel\Configs;

use WrkFlow\ApiSdkBuilder\Log\Entities\LoggersMapEntity;

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
        return new LoggersMapEntity($this->get(keyOrPath: [self::KeyLogging, self::KeyLoggers]));
    }

    public function getLogging(): string
    {
        return $this->get([self::KeyLogging, self::KeyLoggingType]);
    }

    public function isTelescopeEnabled(): bool
    {
        return (bool) $this->get(keyOrPath: self::KeyIsTelescopeEnabled);
    }

    public function getTimeForClearSchedule(): ?string
    {
        return $this->get(keyOrPath: [self::KeyLogging, self::KeyTimeForClearLogSchedule], default: null);
    }

    public function getLogFileBaseDirectory(): string
    {
        return $this->get(keyOrPath: [self::KeyLogging, self::KeyLogFileBaseDirectory]);
    }

    public function getKeepLogFilesForDays(): int
    {
        return (int) $this->get(keyOrPath: [self::KeyLogging, self::KeyKeepLogFilesForDays]);
    }

    protected function getConfigFileName(): string
    {
        return 'api_sdk';
    }
}
