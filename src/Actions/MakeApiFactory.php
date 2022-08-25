<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Closure;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Factories\ApiFactory;

class MakeApiFactory
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly SDKContainerFactoryContract $factoryContract,
    ) {
    }

    /**
     * Builds API factory using http discovery package
     */
    public function execute(): ApiFactoryContract
    {
        $request = $this->containerOr(
            RequestFactoryInterface::class,
            fn () => Psr17FactoryDiscovery::findRequestFactory()
        );

        $client = $this->containerOr(ClientInterface::class, fn () => Psr18ClientDiscovery::find());
        $response = $this->containerOr(
            ResponseFactoryInterface::class,
            fn () => Psr17FactoryDiscovery::findResponseFactory()
        );

        $stream = $this->containerOr(
            StreamFactoryInterface::class,
            fn () => Psr17FactoryDiscovery::findStreamFactory()
        );

        $eventDispatcher = $this->container->has(EventDispatcherInterface::class)
            ? $this->container->get(EventDispatcherInterface::class)
            : null;

        return new ApiFactory(
            request: $request,
            client: $client,
            stream: $stream,
            container: $this->factoryContract,
            response: $response,
            eventDispatcher: $eventDispatcher
        );
    }

    /**
     * @template T
     *
     * @param class-string<T> $interface
     * @param Closure():T     $create
     *
     * @return T
     */
    protected function containerOr(string $interface, Closure $create): mixed
    {
        if ($this->container->has($interface)) {
            return $this->container->get($interface);
        }

        return $create();
    }
}
