<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

interface EnvironmentFakeResponseInterface
{
    /**
     * @param class-string<AbstractResponse> $responseClass
     */
    public function getResponse(
        RequestInterface $request,
        string $responseClass,
        ApiFactoryContract $apiFactory,
    ): ?ResponseInterface;
}
