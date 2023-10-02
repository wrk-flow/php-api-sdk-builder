<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi;

use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\EmptyEndpoint;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpoint;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpointInterface;

class TestApi extends AbstractApi
{
    public function headers(): array
    {
        return [];
    }

    public function json(): JsonEndpoint
    {
        return $this->makeEndpoint(JsonEndpoint::class);
    }

    public function jsonViaInterface(): JsonEndpointInterface
    {
        return $this->makeEndpoint(endpoint: JsonEndpointInterface::class, implementation: JsonEndpoint::class);
    }

    public function phpStanShouldReportThis(): JsonEndpointInterface
    {
        return $this->makeEndpoint(endpoint: JsonEndpointInterface::class, implementation: EmptyEndpoint::class);
    }
}
