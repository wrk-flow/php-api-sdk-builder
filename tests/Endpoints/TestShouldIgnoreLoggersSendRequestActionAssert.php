<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Endpoints;

use Closure;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Throwable;
use WrkFlow\ApiSdkBuilder\Contracts\SendRequestActionContract;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use WrkFlow\ApiSdkBuilder\Testing\Exceptions\TestRequestSentException;

final class TestShouldIgnoreLoggersSendRequestActionAssert implements SendRequestActionContract
{
    /**
     * @param array<class-string<ApiLoggerInterface>> $expectedIgnoreLoggers
     */
    public function __construct(
        private readonly Throwable $testException,
        private readonly array $expectedIgnoreLoggers,
    ) {
    }

    public function execute(
        ApiInterface $api,
        RequestInterface $request,
        string $responseClass,
        StreamInterface|string|OptionsInterface|null $body = null,
        array $headers = [],
        int|array|null $expectedResponseStatusCode = null,
        ?ResponseInterface $fakedResponse = null,
        ?Closure $shouldIgnoreLoggersOnError = null
    ): AbstractResponse {
        Assert::assertNotNull($shouldIgnoreLoggersOnError, 'AbstractEndpoint always builds closure');
        Assert::assertEquals($this->expectedIgnoreLoggers, $shouldIgnoreLoggersOnError($this->testException));

        throw new TestRequestSentException();
    }
}
