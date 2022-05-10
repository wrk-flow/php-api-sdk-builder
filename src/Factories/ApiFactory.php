<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Factories;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;

class ApiFactory
{
    public function __construct(
        private readonly RequestFactoryInterface $request,
        private readonly ClientInterface $client,
        private readonly StreamFactoryInterface $stream,
        private readonly SDKContainerFactoryContract $container,
    ) {
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
}
