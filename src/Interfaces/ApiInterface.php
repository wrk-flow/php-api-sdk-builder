<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use Closure;
use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

interface ApiInterface extends HeadersInterface
{
    public function environment(): AbstractEnvironment;

    public function factory(): ApiFactoryContract;

    public function uri(): Uri;

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                            $responseClass
     * @param array<int|string,HeadersInterface|string|string[]> $headers
     * @param Closure(Throwable):array<ApiLoggerInterface>|null  $shouldIgnoreLoggersOnError
     *
     * @return TResponse
     */
    public function get(
        string $responseClass,
        Uri $uri,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse;

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                            $responseClass
     * @param array<int|string,HeadersInterface|string|string[]> $headers
     * @param int|null                                           $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     * @param Closure(Throwable):array<ApiLoggerInterface>|null  $shouldIgnoreLoggersOnError
     *
     * @return TResponse
     */
    public function post(
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse;

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                            $responseClass
     * @param array<int|string,HeadersInterface|string|string[]> $headers
     * @param int|null                                           $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     * @param Closure(Throwable):array<ApiLoggerInterface>|null  $shouldIgnoreLoggersOnError
     *
     * @return TResponse
     */
    public function put(
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse;

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                            $responseClass
     * @param array<int|string,HeadersInterface|string|string[]> $headers
     * @param int|null                                           $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     * @param Closure(Throwable):array<ApiLoggerInterface>|null  $shouldIgnoreLoggersOnError
     *
     * @return TResponse
     */
    public function delete(
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse;

    /**
     * Sends a fake request with fake response (will all events in place).
     *
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                            $responseClass
     * @param array<int|string,HeadersInterface|string|string[]> $headers
     * @param int|null                                           $expectedResponseStatusCode Will raise and failed
     * exception if response
     * @param Closure(Throwable):array<ApiLoggerInterface>|null  $shouldIgnoreLoggersOnError
     *
     * @return TResponse
     */
    public function fake(
        ResponseInterface $response,
        string $responseClass,
        Uri $uri,
        OptionsInterface|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse;

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException;
}
