<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Loggers;

use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerFailConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

class StackLogger implements ApiLoggerInterface
{
    /**
     * @param array<ApiLoggerInterface> $loggers
     */
    public function __construct(
        private readonly array $loggers,
    ) {
    }

    public function requestFailed(RequestFailedEvent $event, LoggerFailConfigEntity $config): void
    {
        foreach ($this->loggers as $logger) {
            if ($config->shouldIgnoreLogger(logger: $logger)) {
                continue;
            }

            $logger->requestFailed(event: $event, config: $config);
        }
    }

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void
    {
        foreach ($this->loggers as $logger) {
            $logger->requestConnectionFailed(event: $event, config: $config);
        }
    }

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void
    {
        foreach ($this->loggers as $logger) {
            $logger->responseReceivedEvent(event: $event, config: $config);
        }
    }
}
