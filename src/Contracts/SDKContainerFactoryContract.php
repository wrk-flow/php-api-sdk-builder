<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\EndpointInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;

interface SDKContainerFactoryContract
{
    /**
     * @template T of EndpointInterface
     *
     * @param class-string<T> $endpointClass
     *
     * @return T
     */
    public function makeEndpoint(ApiInterface $api, string $endpointClass): EndpointInterface;

    /**
     * Dynamically creates an instance of the given class.
     * - Some classes should be cached for performance (as singletons).
     * @template T of object
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function make(string $class): mixed;

    /**
     * Checks if the container has a binding for the given class or key.
     *
     * @param class-string|string $classOrKey
     */
    public function has(string $classOrKey): bool;

    /**
     * @template T of AbstractResponse
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function makeResponse(string $class, ResponseInterface $response, ?GetValue $body): AbstractResponse;
}
