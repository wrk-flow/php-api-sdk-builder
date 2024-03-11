<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Throwable;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

/**
 * @phpstan-type IgnoreLoggersOnExceptionClosure Closure(Throwable):(array<class-string<ApiLoggerInterface>>)|null
 */
interface ApiInterface extends HeadersInterface
{
    /**
     * @return IgnoreLoggersOnExceptionClosure
     */
    public function shouldIgnoreLoggersOnException(): ?Closure;

    public function environment(): AbstractEnvironment;

    public function factory(): ApiFactoryContract;

    public function uri(): UriInterface;

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException;
}
