<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel;

use Illuminate\Contracts\Container\Container;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\EndpointInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;

class LaravelContainerFactory implements SDKContainerFactoryContract
{
    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * @template T of EndpointInterface
     * @param class-string<T> $endpointClass
     *
     * @return T
     */
    public function makeEndpoint(ApiInterface $api, string $endpointClass): EndpointInterface
    {
        $endpoint = $this->container->make($endpointClass, [
            'di' => $this->container->make(EndpointDIEntity::class, [
                'api' => $api,
            ]),
        ]);

        assert($endpoint instanceof $endpointClass);

        return $endpoint;
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     *
     * @return T
     */
    public function make(string $class): object
    {
        $object = $this->container->make($class);
        assert($object instanceof $class);
        return $object;
    }

    public function has(string $classOrKey): bool
    {
        return $this->container->has($classOrKey);
    }

    /**
     * @template T of AbstractResponse
     * @param class-string<T> $class
     *
     * @return T
     */
    public function makeResponse(string $class, ResponseInterface $response, ?GetValue $body): AbstractResponse
    {
        $apiResponse = $this->container->make($class, [
            'response' => $response,
            'container' => $this,
            'body' => $body,
        ]);
        assert($apiResponse instanceof $class);
        return $apiResponse;
    }
}
