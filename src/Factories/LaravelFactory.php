<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Factories;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Containers\LaravelContainerFactory;

/**
 * Factory that we can create with Laravel container.
 */
class LaravelFactory extends ApiFactory
{
    public function __construct(
        LaravelContainerFactory $laravelContainer,
        RequestFactoryInterface $request,
        ClientInterface $client,
        StreamFactoryInterface $stream
    ) {
        parent::__construct(
            request: $request,
            client: $client,
            stream: $stream,
            container: $laravelContainer
        );
    }
}
