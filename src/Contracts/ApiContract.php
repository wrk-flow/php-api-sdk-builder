<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

interface ApiContract extends HeadersContract
{
    public function environment(): AbstractEnvironment;

    public function factory(): ApiFactoryContract;

    public function uri(): Uri;

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                           $responseClass
     * @param array<int|string,HeadersContract|string|string[]> $headers
     *
     * @return TResponse
     */
    public function get(
        string $responseClass,
        Uri $uri,
        array $headers = [],
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse;

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
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse;

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
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse;

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
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse;

    /**
     * Sends a fake request with fake response (will all events in place).
     *
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>                           $responseClass
     * @param array<int|string,HeadersContract|string|string[]> $headers
     * @param int|null                                          $expectedResponseStatusCode Will raise and failed
     * exception if response
     *
     * @return TResponse
     */
    public function fake(
        ResponseInterface $response,
        string $responseClass,
        Uri $uri,
        OptionsContract|StreamInterface|string $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse;

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException;
}
