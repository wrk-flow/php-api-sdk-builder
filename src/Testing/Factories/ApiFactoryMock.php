<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Factories;

use Mockery;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;

class ApiFactoryMock implements ApiFactoryContract
{
    public function request(): RequestFactoryInterface
    {
        return Mockery::mock(RequestFactoryInterface::class);
    }

    public function client(): ClientInterface
    {
        return Mockery::mock(ClientInterface::class);
    }

    public function stream(): StreamFactoryInterface
    {
        return new StreamFactoryMock();
    }

    public function container(): SDKContainerFactoryContract
    {
        return Mockery::mock(SDKContainerFactoryContract::class);
    }

    public function eventDispatcher(): ?EventDispatcherInterface
    {
        return Mockery::mock(EventDispatcherInterface::class);
    }
}
