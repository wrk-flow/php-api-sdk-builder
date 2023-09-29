<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Entities;

use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

final class LoggerFailConfigEntity
{
    public function __construct(
        public readonly LoggerConfigEntity $config,
        public readonly array $ignoreLoggersOnFail = [],
    ) {
    }

    public function shouldIgnoreLogger(ApiLoggerInterface $logger): bool
    {
        foreach ($this->ignoreLoggersOnFail as $ignoreLogger) {
            if ($logger instanceof $ignoreLogger) {
                return true;
            }
        }

        return false;
    }
}
