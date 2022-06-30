<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

interface SDKContainerFactoryContract
{
    /**
     * @template T of AbstractEndpoint
     *
     * @param class-string<T> $endpointClass
     *
     * @return T
     */
    public function makeEndpoint(AbstractApi $api, string $endpointClass): AbstractEndpoint;

    /**
     * @template T
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function make(string $class): mixed;

    /**
     * @template T of AbstractResponse
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    public function makeResponse(string $class, ResponseInterface $response, mixed $body): AbstractResponse;
}
