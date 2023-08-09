<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi;

use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpoint;

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
}
