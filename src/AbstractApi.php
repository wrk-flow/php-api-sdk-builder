<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\BadRequestException;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Exceptions\ServerFailedException;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\EndpointInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\EnvironmentOverrideEndpointsInterface;

abstract class AbstractApi implements ApiInterface
{
    /**
     * A cache map of created endpoints: class -> instance.
     *
     * @var array<string,EndpointInterface>
     */
    private array $cachedEndpoints = [];

    /**
     * @var array<class-string<EndpointInterface>, class-string<EndpointInterface>>
     */
    private readonly array $overrideEndpoints;


    public function __construct(
        private readonly AbstractEnvironment $environment,
        private readonly ApiFactoryContract $factory
    ) {
        $this->overrideEndpoints = $environment instanceof EnvironmentOverrideEndpointsInterface ? $environment->endpoints() : [];
    }

    final public function environment(): AbstractEnvironment
    {
        // Makes the environment testable
        return $this->environment;
    }

    final public function factory(): ApiFactoryContract
    {
        // Makes the factory testable
        return $this->factory;
    }

    final public function uri(): UriInterface
    {
        return $this->environment->uri();
    }

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException
    {
        if ($statusCode >= 400 && $statusCode < 500) {
            return new BadRequestException($response);
        }

        return new ServerFailedException($response);
    }

    public function shouldIgnoreLoggersOnException(): ?Closure
    {
        return null;
    }

    /**
     * @template T of EndpointInterface
     *
     * @param class-string<T> $endpoint
     * @param class-string<T>|null $implementation
     *
     * @return T
     */
    final protected function makeEndpoint(string $endpoint, string $implementation = null): EndpointInterface
    {
        if (array_key_exists($endpoint, $this->cachedEndpoints) === false) {
            $endpoint = $this->getOverrideEndpointClassIfCan($endpoint, $implementation ?? $endpoint);

            $instance = $this
                ->factory()
                ->container()
                ->makeEndpoint($this, $endpoint);

            $endpointInstance = $instance;
            $this->cachedEndpoints[$endpoint] = $instance;
        } else {
            $endpointInstance = $this->cachedEndpoints[$endpoint];
        }

        assert(assertion: $endpointInstance instanceof $endpoint, description: 'Invalid cached endpoints state.');

        return $endpointInstance;
    }

    /**
     * Allow swapping implementation for original endpoint using interface. If the endpoint is in the overrideEndpoints
     * then we will return the override class, otherwise we will return the original $implementation.
     *
     * @template T of EndpointInterface
     *
     * @param class-string<T> $endpoint
     * @param class-string<T> $implementation
     *
     * @return class-string<T>
     */
    private function getOverrideEndpointClassIfCan(string $endpoint, string $implementation): string
    {
        if ($this->overrideEndpoints === []) {
            return $implementation;
        }

        if (array_key_exists($endpoint, $this->overrideEndpoints)) {
            return $this->overrideEndpoints[$endpoint];
        }

        return $implementation;
    }
}
