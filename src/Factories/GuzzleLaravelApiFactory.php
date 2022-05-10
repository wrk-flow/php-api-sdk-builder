<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Factories;

use GuzzleHttp\Client;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use WrkFlow\ApiSdkBuilder\Containers\LaravelContainerFactory;

/**
 * Automatically setup the guzzle with Laravel container. Use dependency injection.
 *
 * Require packages:
 * - guzzlehttp/guzzle^7
 * - http-interop/http-factory-guzzle^7
 */
class GuzzleLaravelApiFactory extends ApiFactory
{
    public function __construct(
        LaravelContainerFactory $laravelContainer,
        RequestFactory $requestFactory,
        Client $client,
        StreamFactory $streamFactory
    ) {
        parent::__construct(
            $requestFactory,
            $client,
            $streamFactory,
            $laravelContainer
        );
    }
}
