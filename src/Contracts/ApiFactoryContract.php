<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

interface ApiFactoryContract
{
    public function request(): RequestFactoryInterface;

    public function client(): ClientInterface;

    public function stream(): StreamFactoryInterface;

    public function response(): ResponseFactoryInterface;

    public function container(): SDKContainerFactoryContract;

    public function eventDispatcher(): ?EventDispatcherInterface;

    public function loggerConfig(): LoggerConfigEntity;
}
