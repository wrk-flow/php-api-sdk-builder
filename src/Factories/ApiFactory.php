<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Factories;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class ApiFactory implements ApiFactoryContract
{
    private readonly LoggerConfigEntity $loggerConfig;

    public function __construct(
        private readonly RequestFactoryInterface $request,
        private readonly ClientInterface $client,
        private readonly StreamFactoryInterface $stream,
        private readonly SDKContainerFactoryContract $container,
        private readonly ResponseFactoryInterface $response,
        private readonly ?EventDispatcherInterface $eventDispatcher = null,
        LoggerConfigEntity $loggerConfig = null,
    ) {
        // By, default no logging is used
        $this->loggerConfig = $loggerConfig ?? new LoggerConfigEntity();
    }

    public function response(): ResponseFactoryInterface
    {
        return $this->response;
    }

    public function request(): RequestFactoryInterface
    {
        return $this->request;
    }

    public function client(): ClientInterface
    {
        return $this->client;
    }

    public function stream(): StreamFactoryInterface
    {
        return $this->stream;
    }

    public function container(): SDKContainerFactoryContract
    {
        return $this->container;
    }

    public function eventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function loggerConfig(): LoggerConfigEntity
    {
        return $this->loggerConfig;
    }
}
