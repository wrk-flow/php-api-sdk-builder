<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Endpoints;

use Closure;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Testing\Assertions\SendTestRequestActionAssert;
use WrkFlow\ApiSdkBuilder\Testing\Exceptions\TestRequestSentException;
use WrkFlow\ApiSdkBuilder\Testing\Factories\EndpointDIEntityFactory;

abstract class EndpointTestCase extends MockeryTestCase
{
    protected function assertEndpoint(
        Closure $call,
        string $expectedUri,
        string $expectedMethod = 'GET',
        StreamInterface|string|OptionsInterface|array|null $expectedBody = null,
        array $expectedHeaders = [],
        ?int $expectedResponseStatusCode = null,
    ): void {
        $this->expectException(TestRequestSentException::class);

        $di = EndpointDIEntityFactory::make(
            sendAssert: new SendTestRequestActionAssert(
                expectedUri: $expectedUri,
                expectedMethod: $expectedMethod,
                expectedBody: $expectedBody,
                expectedHeaders: $expectedHeaders,
                expectedResponseStatusCode: $expectedResponseStatusCode,
            ),
        );
        $call($di);
    }
}
