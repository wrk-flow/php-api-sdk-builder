<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder;

use Closure;
use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Actions\SendRequestAction;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\BadRequestException;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Exceptions\ServerFailedException;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\EnvironmentOverrideEndpointsInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

abstract class AbstractApi implements ApiInterface
{
    /**
     * A cache map of created endpoints: class -> instance.
     *
     * @var array<string,AbstractEndpoint>
     */
    private array $cachedEndpoints = [];

    private ?SendRequestAction $sendRequestAction = null;

    /**
     * @var array<class-string, class-string<AbstractEndpoint>>
     */
    private readonly array $overrideEndpoints;

    /**
     * @param array<class-string, class-string<AbstractEndpoint>> $overrideEndpoints
     */
    public function __construct(
        private readonly AbstractEnvironment $environment,
        private readonly ApiFactoryContract $factory,
        array $overrideEndpoints = []
    ) {
        $this->overrideEndpoints = array_merge(
            $overrideEndpoints,
            $environment instanceof EnvironmentOverrideEndpointsInterface ? $environment->endpoints() : []
        );
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

    final public function uri(): Uri
    {
        return $this->environment->uri();
    }

    final public function get(
        string $responseClass,
        Uri $uri,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('GET', $uri->toString());

        return $this->sendRequestAction()
            ->execute(
                api: $this,
                request: $request,
                responseClass: $responseClass,
                headers: $headers,
                expectedResponseStatusCode: $expectedResponseStatusCode,
                shouldIgnoreLoggersOnError: $shouldIgnoreLoggersOnError,
            );
    }

    final public function post(
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('POST', $uri->toString());

        return $this->sendRequestAction()
            ->execute(
                api: $this,
                request: $request,
                responseClass: $responseClass,
                body: $body,
                headers: $headers,
                expectedResponseStatusCode: $expectedResponseStatusCode,
                shouldIgnoreLoggersOnError: $shouldIgnoreLoggersOnError,
            );
    }

    final public function put(
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('PUT', $uri->toString());

        return $this->sendRequestAction()
            ->execute(
                api: $this,
                request: $request,
                responseClass: $responseClass,
                body: $body,
                headers: $headers,
                expectedResponseStatusCode: $expectedResponseStatusCode,
                shouldIgnoreLoggersOnError: $shouldIgnoreLoggersOnError,
            );
    }

    final public function delete(
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('DELETE', $uri->toString());

        return $this->sendRequestAction()
            ->execute(
                api: $this,
                request: $request,
                responseClass: $responseClass,
                body: $body,
                headers: $headers,
                expectedResponseStatusCode: $expectedResponseStatusCode,
                shouldIgnoreLoggersOnError: $shouldIgnoreLoggersOnError,
            );
    }

    public function fake(
        ResponseInterface $response,
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse {
        return $this->sendRequestAction()
            ->execute(
                api: $this,
                request: $this->factory()
                    ->request()
                    ->createRequest('FAKE', $uri->toString()),
                responseClass: $responseClass,
                body: $body,
                headers: $headers,
                expectedResponseStatusCode: $expectedResponseStatusCode,
                fakedResponse: $response,
                shouldIgnoreLoggersOnError: $shouldIgnoreLoggersOnError,
            );
    }

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException
    {
        if ($statusCode >= 400 && $statusCode < 500) {
            return new BadRequestException($response);
        }

        return new ServerFailedException($response);
    }

    /**
     * @template T of AbstractEndpoint
     *
     * @param class-string<T> $endpoint
     *
     * @return T
     */
    final protected function makeEndpoint(string $endpoint): AbstractEndpoint
    {
        if (array_key_exists($endpoint, $this->cachedEndpoints) === false) {
            $endpoint = $this->getOverrideEndpointClassIfCan($endpoint);

            $instance = $this->factory()
                ->container()
                ->makeEndpoint($this, $endpoint);

            $this->cachedEndpoints[$endpoint] = $instance;
        }

        return $this->cachedEndpoints[$endpoint];
    }

    /**
     * @template T of AbstractEndpoint
     *
     * Allow swapping implementation for original endpoint using interface We will receive the "real" endpoint
     * implementation we will check if the endpoint implements any interface check the interface agains override
     * endpoints map
     *
     * @param class-string<T> $endpoint
     *
     * @return class-string<T>
     */
    private function getOverrideEndpointClassIfCan(string $endpoint): string
    {
        if ($this->overrideEndpoints === []) {
            return $endpoint;
        }

        $implements = class_implements($endpoint);

        if (is_array($implements) === false) {
            return $endpoint;
        }

        foreach ($implements as $interface) {
            if (array_key_exists($interface, $this->overrideEndpoints)) {
                return $this->overrideEndpoints[$interface];
            }
        }

        return $endpoint;
    }

    private function sendRequestAction(): SendRequestAction
    {
        if ($this->sendRequestAction instanceof SendRequestAction === false) {
            $this->sendRequestAction = $this->factory()
                ->container()
                ->make(SendRequestAction::class);
        }

        return $this->sendRequestAction;
    }
}
