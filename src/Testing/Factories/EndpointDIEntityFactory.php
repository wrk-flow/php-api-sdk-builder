<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Factories;

use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Testing\ApiMock;
use WrkFlow\ApiSdkBuilder\Testing\Assertions\SendTestRequestActionAssert;

final class EndpointDIEntityFactory
{
    public static function make(
        ApiInterface $api = new ApiMock(),
        SendTestRequestActionAssert $sendAssert = new SendTestRequestActionAssert()
    ): EndpointDIEntity {
        return new EndpointDIEntity(api: $api, sendRequestAction: $sendAssert);
    }
}
