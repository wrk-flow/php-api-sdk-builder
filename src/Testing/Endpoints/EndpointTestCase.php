<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Endpoints;

use Closure;
use JustSteveKing\UriBuilder\Uri;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use WrkFlow\ApiSdkBuilder\Testing\ApiMock;

abstract class EndpointTestCase extends MockeryTestCase
{
    protected ApiMock $api;

    protected Uri $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = new ApiMock();
    }

    public function assertGet(EndpointExpectation $expectation, Closure $callEndpoint): void
    {
        $this->api->getExpectations[] = $expectation;
        $this->assertEndpointCall($callEndpoint);
    }

    public function assertPost(EndpointExpectation $expectation, Closure $callEndpoint): void
    {
        $this->api->postExpectations[] = $expectation;
        $this->assertEndpointCall($callEndpoint);
    }

    public function assertPut(EndpointExpectation $expectation, Closure $callEndpoint): void
    {
        $this->api->putExpectations[] = $expectation;
        $this->assertEndpointCall($callEndpoint);
    }

    public function assertDelete(EndpointExpectation $expectation, Closure $callEndpoint): void
    {
        $this->api->deleteExpectations[] = $expectation;
        $this->assertEndpointCall($callEndpoint);
    }

    private function assertEndpointCall(Closure $callEndpoint): void
    {
        $this->assertInstanceOf(MockInterface::class, $callEndpoint(), 'Endpoint response mock not returned');
    }
}
