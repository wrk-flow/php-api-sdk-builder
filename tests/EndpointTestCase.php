<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests;

use Closure;
use JustSteveKing\UriBuilder\Uri;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\ExpectationInterface;
use Mockery\MockInterface;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\ApiFactory;

abstract class EndpointTestCase extends MockeryTestCase
{
    /**
     * @var string
     */
    final public const BASE_URI = 'https://localhost/test';

    protected MockInterface $api;

    protected MockInterface $apiFactory;

    protected Uri $uri;

    protected function setUp(): void
    {
        parent::setUp();

        $this->api = Mockery::mock(AbstractApi::class);
        $this->uri = Uri::fromString(self::BASE_URI);
        $this->api->shouldReceive('uri')
            ->andReturn($this->uri);
        $this->apiFactory = Mockery::mock(ApiFactory::class);
        $this->api->shouldReceive('factory')
            ->andReturn($this->apiFactory);
    }

    /**
     * @param string $expectedUri expected url from the base url
     * @param Closure(mixed):bool|null $assertBody    Checks the body. Return false if not valid.
     * @param Closure(mixed):bool|null $assertHeaders Checks the headers. Return false if not valid.
     */
    protected function expectPost(
        string $expectedUri,
        ?Closure $assertBody = null,
        ?Closure $assertHeaders = null,
    ): ExpectationInterface {
        return $this->api->shouldReceive('post')
            ->once()
            ->withArgs(function () use ($expectedUri, $assertBody, $assertHeaders) {
                /** @var Uri $uri */
                $uri = func_get_arg(0);
                if ($uri->toString() !== $expectedUri) {
                    return false;
                }

                if ($assertBody !== null && $assertBody(func_get_arg(1)) === false) {
                    return false;
                }

                if ($assertHeaders !== null && $assertHeaders(func_get_arg(2)) === false) {
                    return false;
                }

                return true;
            });
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
