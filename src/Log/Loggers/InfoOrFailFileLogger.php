<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Loggers;

use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\InfoLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\InfoOrFailFileLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class InfoOrFailFileLogger implements InfoOrFailFileLoggerContract
{
    public function __construct(
        private readonly InfoLoggerContract $logger,
        private readonly FileLoggerContract $fileLogger
    ) {
    }

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void
    {
        $this->logger->responseReceivedEvent($event, $config);
    }

    public function requestFailed(RequestFailedEvent $event, LoggerConfigEntity $config): void
    {
        $this->logger->requestFailed($event, $config);
        $this->fileLogger->requestFailed($event, $config);
    }

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void
    {
        $this->logger->requestConnectionFailed($event, $config);
        $this->fileLogger->requestConnectionFailed($event, $config);
    }
}
