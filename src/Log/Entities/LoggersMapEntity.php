<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Entities;

use WrkFlow\ApiSdkBuilder\Log\Contracts\LoggerContract;

/**
 * Just a simple entity to hold the loggers map and its typehint.
 */
class LoggersMapEntity
{
    /**
     * @param array<string,array<class-string<LoggerContract>>> $loggers Can be empty if nothing should be logged.
     */
    public function __construct(
        public readonly array $loggers,
    ) {
    }
}
