<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing;

use Closure;
use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Testing\Environments\TestingEnvironment;
use WrkFlow\ApiSdkBuilder\Testing\Factories\ApiFactoryMock;

final class ApiMock implements ApiInterface
{
    public function shouldIgnoreLoggersOnException(): ?Closure
    {
        return null;
    }

    public function environment(): AbstractEnvironment
    {
        return new TestingEnvironment();
    }

    public function factory(): ApiFactoryContract
    {
        return new ApiFactoryMock();
    }

    public function uri(): Uri
    {
        return Uri::fromString('https://test.localhost');
    }

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException
    {
        return new ResponseException($response);
    }

    public function headers(): array
    {
        return [];
    }
}
