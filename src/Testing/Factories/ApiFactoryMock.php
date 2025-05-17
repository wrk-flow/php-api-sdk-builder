<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Factories;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Mock\Client;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class ApiFactoryMock implements ApiFactoryContract
{
    public readonly ResponseFactoryInterface $responseFactory;
    public readonly RequestFactoryInterface $requestFactory;

    public function __construct(
        public readonly Client $mockClient = new Client(),
        ?ResponseFactoryInterface $responseFactory = null,
        ?RequestFactoryInterface $requestFactory = null,
        public readonly ?EventDispatcherInterface $eventDispatcher = null,
        public readonly TestSDKContainerFactory $testSDKContainerFactory = new TestSDKContainerFactory(),
    ) {
        $this->responseFactory = $responseFactory ?? Psr17FactoryDiscovery::findResponseFactory();
        $this->requestFactory = $requestFactory ?? Psr17FactoryDiscovery::findRequestFactory();
    }


    public function response(): ResponseFactoryInterface
    {
        return $this->responseFactory;
    }

    public function request(): RequestFactoryInterface
    {
        return $this->requestFactory;
    }

    public function client(): ClientInterface
    {
        return $this->mockClient;
    }

    public function stream(): StreamFactoryInterface
    {
        return new StreamFactoryMock();
    }

    public function container(): SDKContainerFactoryContract
    {
        return $this->testSDKContainerFactory;
    }

    public function eventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }

    public function loggerConfig(): LoggerConfigEntity
    {
        return new LoggerConfigEntity(logger: '');
    }
}
