<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Closure;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Factories\ApiFactory;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class MakeApiFactory
{
    public function __construct(
        private readonly SDKContainerFactoryContract $container,
    ) {
    }

    /**
     * Builds API factory using http discovery package
     */
    public function execute(LoggerConfigEntity $loggerConfig = null): ApiFactoryContract
    {
        $request = $this->containerOr(
            interface: RequestFactoryInterface::class,
            create: static fn () => Psr17FactoryDiscovery::findRequestFactory()
        );

        $client = $this->containerOr(ClientInterface::class, static fn () => Psr18ClientDiscovery::find());
        $response = $this->containerOr(
            interface: ResponseFactoryInterface::class,
            create: static fn () => Psr17FactoryDiscovery::findResponseFactory()
        );

        $stream = $this->containerOr(
            interface: StreamFactoryInterface::class,
            create: static fn () => Psr17FactoryDiscovery::findStreamFactory()
        );

        $eventDispatcher = $this->container->has(EventDispatcherInterface::class)
            ? $this->container->make(EventDispatcherInterface::class)
            : null;

        return new ApiFactory(
            request: $request,
            client: $client,
            stream: $stream,
            container: $this->container,
            response: $response,
            eventDispatcher: $eventDispatcher,
            loggerConfig: $loggerConfig
        );
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $interface
     * @param Closure():T     $create
     *
     * @return T
     */
    protected function containerOr(string $interface, Closure $create): mixed
    {
        if ($this->container->has($interface)) {
            return $this->container->make($interface);
        }

        return $create();
    }
}
