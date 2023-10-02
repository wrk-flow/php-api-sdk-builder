<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Testing\Endpoints;

use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Testing\Endpoints\EndpointTestCase;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonEndpoint;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json\JsonOptions;

class EndpointTestCaseTest extends EndpointTestCase
{
    private const ExpectedHeaders = [
        'Content-Type' => ['application/json'],
        'Accept' => ['application/json'],
    ];

    public function testEndpointGet(): void
    {
        $this->assertEndpoint(
            call: static fn (EndpointDIEntity $di) => (new JsonEndpoint($di))->success(),
            expectedUri: '/json',
            expectedHeaders: self::ExpectedHeaders,
        );
    }

    public function testStoreWithoutOptions(): void
    {
        $this->assertEndpoint(
            call: static fn (EndpointDIEntity $di) => (new JsonEndpoint($di))->store(),
            expectedMethod: 'POST',
            expectedUri: '/json',
            expectedHeaders: self::ExpectedHeaders,
        );
    }

    public function testStoreWithJsonOptions(): void
    {
        $data = new JsonOptions(input: 'input', keys: [1, 2, 3]);
        $this->assertEndpoint(
            call: static fn (EndpointDIEntity $di) => (new JsonEndpoint($di))->store($data),
            expectedUri: '/json',
            expectedMethod: 'POST',
            expectedBody: $data,
            expectedHeaders: self::ExpectedHeaders,
        );
    }
}
