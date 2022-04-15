<?php

declare(strict_types=1);

namespace Tests\WrkFlow\ApiSdkBuilder;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Http\Message\ResponseInterface;

abstract class ResponseTestCase extends MockeryTestCase
{
    protected function createJsonResponse(array $response): ResponseInterface
    {
        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn(json_encode($response, JSON_THROW_ON_ERROR));

        return $responseMock;
    }
}
