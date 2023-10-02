<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilderTests\TestApi\FakeEnvironment;

class TestFakeApiTest extends ApiTestCase
{
    public function testJsonSuccessRealImplementation(): void
    {
        $response = $this->api
            ->json()
            ->success();

        $this->assertEquals(true, $response->success);
    }

    public function testJsonViaInterfaceUsesFakeImplementation(): void
    {
        $response = $this->api
            ->jsonViaInterface()
            ->success();

        $this->assertEquals(false, $response->success);
    }
    protected function createApiEnvironment(): AbstractEnvironment
    {
        return new FakeEnvironment();
    }
}
