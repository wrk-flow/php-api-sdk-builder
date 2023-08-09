<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Laravel;

use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

class TestApiTest extends ApiTestCase
{
    public function testSuccessPrintsOnlyToLogger(): void
    {
        $response = $this->api->json()
            ->success();

        $this->assertEquals(true, $response->success);
    }

    public function testFailOnServerUsesInfoAndFileLogger(): void
    {
        $this->expectExceptionMessage('Server failed');

        $this->api->json()
            ->failOnServer();
    }

    public function testFailOnStatusCode400(): void
    {
        $this->expectExceptionMessage('Server response indicates bad request with status code of 400');

        $this->api->json()
            ->failOnStatusCode(statusCode: 400);
    }

    public function testFailOnStatusCode500(): void
    {
        $this->expectExceptionMessage('Server response indicates bad server issue with status code of 500');

        $this->api->json()
            ->failOnStatusCode(statusCode: 500);
    }
    protected function mockBeforeApiFactory(): void
    {
        parent::mockBeforeApiFactory();

        $this->app()
            ->bind(
                LoggerConfigEntity::class,
                static fn () => new LoggerConfigEntity(logger: '*:' . LoggerConstants::NoLog)
            );
    }
}
