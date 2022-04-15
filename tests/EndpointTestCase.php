<?php

declare(strict_types=1);

namespace Tests\WrkFlow\ApiSdkBuilder;

use JustSteveKing\UriBuilder\Uri;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\ApiFactory;

abstract class EndpointTestCase extends MockeryTestCase
{
    protected MockInterface $api;
    protected MockInterface $apiFactory;
    protected Uri $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = Mockery::mock(AbstractApi::class);
        $this->uri = Uri::fromString('https://localhost');
        $this->api->shouldReceive('uri')
            ->andReturn($this->uri);
        $this->apiFactory = Mockery::mock(ApiFactory::class);
        $this->api->shouldReceive('factory')
            ->andReturn($this->apiFactory);
    }

    protected function expectPost(): ExpectationInterface
    {
        return $this->api->shouldReceive('post')
            ->once();
    }

    protected function expectMakeResponse(
        string $expectedClass,
        ExpectationInterface $expectedResponse
    ): MockInterface {
        $return = Mockery::mock($expectedClass);
        $this->apiFactory->shouldReceive('container->makeResponse')
            ->once()
            ->withArgs(function ($class, $response) use ($expectedClass, $expectedResponse) {
                if ($class !== $expectedClass) {
                    return false;
                }

                return $response !== $expectedResponse;
            })
            ->andReturn($return);

        return $return;
    }
}
