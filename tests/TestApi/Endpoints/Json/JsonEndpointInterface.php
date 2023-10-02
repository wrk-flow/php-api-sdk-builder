<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json;

use WrkFlow\ApiSdkBuilder\Interfaces\EndpointInterface;

interface JsonEndpointInterface extends EndpointInterface
{
    public function success(): JsonResponse;
}
