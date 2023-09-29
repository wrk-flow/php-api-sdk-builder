<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Interfaces;

use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerFailConfigEntity;

interface ApiLoggerInterface
{
    public function requestFailed(RequestFailedEvent $event, LoggerFailConfigEntity $config): void;

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void;

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void;
}
