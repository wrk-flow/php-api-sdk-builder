<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi;

use WrkFlow\ApiSdkBuilder\Interfaces\EnvironmentOverrideEndpointsInterface;
use WrkFlow\ApiSdkBuilder\Testing\Environments\TestingEnvironment;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\FakeJsonEndpoint;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpointInterface;

final class FakeEnvironment extends TestingEnvironment implements EnvironmentOverrideEndpointsInterface
{
    public function endpoints(): array
    {
        return [
            JsonEndpointInterface::class => FakeJsonEndpoint::class,
        ];
    }
}
