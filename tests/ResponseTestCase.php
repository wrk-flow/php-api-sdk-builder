<?php

declare(strict_types=1);

namespace Tests\WrkFlow\ApiSdkBuilder;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;

abstract class ResponseTestCase extends MockeryTestCase
{
    protected MockInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = Mockery::mock(SDKContainerFactoryContract::class);
    }

    protected function createTransformerMockViaContainer(string $expectedTransformerClass): MockInterface
    {
        $transformer = Mockery::mock($expectedTransformerClass);

        $this->container->shouldReceive('make')
            ->once()
            ->with($expectedTransformerClass)
            ->andReturn($transformer);

        return $transformer;
    }

    protected function createJsonResponse(array $response): ResponseInterface
    {
        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($response, JSON_THROW_ON_ERROR));

        return $responseMock;
    }
}
