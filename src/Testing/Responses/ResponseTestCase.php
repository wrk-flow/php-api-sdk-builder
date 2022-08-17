<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Responses;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;

abstract class ResponseTestCase extends MockeryTestCase
{
    protected MockInterface $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = Mockery::mock(SDKContainerFactoryContract::class);
    }

    protected function expectContainerMake(object $object): void
    {
        $this->container->shouldReceive('make')
            ->once()
            ->with($object::class)
            ->andReturn($object);
    }
}
