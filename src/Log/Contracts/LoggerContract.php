<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Contracts;

use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

interface LoggerContract
{
    public function requestFailed(RequestFailedEvent $event, LoggerConfigEntity $config): void;

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void;

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void;
}
