<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;

class ApiFactory
{
    public function __construct(
        public readonly RequestFactoryInterface $request,
        public readonly ClientInterface $client,
        public readonly StreamFactoryInterface $stream,
        public readonly SDKContainerFactoryContract $container,
    ) {
    }
}
