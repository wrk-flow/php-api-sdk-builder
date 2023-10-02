<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json;

use WrkFlow\ApiSdkBuilder\Endpoints\AbstractFakeEndpoint;
use Wrkflow\GetValue\DataHolders\ArrayData;
use Wrkflow\GetValue\GetValue;

final class FakeJsonEndpoint extends AbstractFakeEndpoint implements JsonEndpointInterface
{
    public function success(): JsonResponse
    {
        return $this->makeResponse(
            responseClass: JsonResponse::class,
            responseBody: new GetValue(new ArrayData([
                JsonResponse::KeySuccess => false,
            ]))
        );
    }
}
