<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder;

use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeadersAction;
use WrkFlow\ApiSdkBuilder\Actions\MakeBodyFromResponseAction;
use WrkFlow\ApiSdkBuilder\Actions\SendRequestAction;
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;
use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\BadRequestException;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Exceptions\ServerFailedException;
use WrkFlow\ApiSdkBuilder\Factories\ApiFactory;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

abstract class AbstractApi implements HeadersContract
{
    /**
     * A cache map of created endpoints: class -> instance.
     *
     * @var array<string,AbstractEndpoint>
     */
    protected array $cachedEndpoints = [];

    private readonly SendRequestAction $sendRequestAction;

    public function __construct(
        private readonly AbstractEnvironment $environment,
        private readonly ApiFactory $factory,
    ) {
        $container = $this->factory()
            ->container();

        $this->sendRequestAction = $container->make(SendRequestAction::class);
    }

    public function environment(): AbstractEnvironment
    {
        // Makes the environment testable
        return $this->environment;
    }

    public function factory(): ApiFactory
    {
        // Makes the factory testable
        return $this->factory;
    }

    public function uri(): Uri
    {
        return $this->environment->uri();
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param array<int|string,HeadersContract|string|string[]> $headers
     *
     * @return TResponse
     */
    public function get(
        string $responseClass,
        Uri $uri,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('GET', $uri->toString());

        return $this->sendRequestAction
            ->execute($this, $request, $responseClass, null, $headers, $expectedResponseStatusCode);
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                           $responseClass
     * @param array<int|string,HeadersContract|string|string[]> $headers
     * @param int|null                                          $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     *
     * @return TResponse
     */
    public function post(
        string $responseClass,
        Uri $uri,
        OptionsContract|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('POST', $uri->toString());

        return $this->sendRequestAction
            ->execute($this, $request, $responseClass, $body, $headers, $expectedResponseStatusCode);
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                           $responseClass
     * @param array<int|string,HeadersContract|string|string[]> $headers
     * @param int|null                                          $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     *
     * @return TResponse
     */
    public function put(
        string $responseClass,
        Uri $uri,
        OptionsContract|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('PUT', $uri->toString());

        return $this->sendRequestAction
            ->execute($this, $request, $responseClass, $body, $headers, $expectedResponseStatusCode);
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                           $responseClass
     * @param array<int|string,HeadersContract|string|string[]> $headers
     * @param int|null                                          $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     *
     * @return TResponse
     */
    public function delete(
        string $responseClass,
        Uri $uri,
        OptionsContract|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $request = $this->factory()
            ->request()
            ->createRequest('DELETE', $uri->toString());

        return $this->sendRequestAction
            ->execute($this, $request, $responseClass, $body, $headers, $expectedResponseStatusCode);
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
    protected function makeEndpoint(string $endpoint): AbstractEndpoint
    {
        if (array_key_exists($endpoint, $this->cachedEndpoints) === false) {
            $instance = $this->factory()
                ->container()
                ->makeEndpoint($this, $endpoint);

            $this->cachedEndpoints[$endpoint] = $instance;
        }

        return $this->cachedEndpoints[$endpoint];
    }
}
