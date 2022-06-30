<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Testing\Responses\JsonResponse;

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
        return new JsonResponse($response);
    }
}
