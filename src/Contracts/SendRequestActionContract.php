<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use Closure;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

/**
 * @phpstan-import-type IgnoreLoggersOnExceptionClosure from ApiInterface
 */
interface SendRequestActionContract
{
    /**
     * @template TResponse of AbstractResponse
     *
     * @param array<int|string,HeadersInterface|string|string[]> $headers
     * @param class-string<TResponse>                            $responseClass
     * @param int|null                                           $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     *                                                                                      status code is different
     * @param IgnoreLoggersOnExceptionClosure                    $shouldIgnoreLoggersOnError
     * @return TResponse
     */
    public function execute(
        ApiInterface $api,
        RequestInterface $request,
        string $responseClass,
        OptionsInterface|StreamInterface|string|null $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        ?ResponseInterface $fakedResponse = null,
        Closure $shouldIgnoreLoggersOnError = null
    ): AbstractResponse;
}
