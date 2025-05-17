<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Assertions;

use Closure;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeadersAction;
use WrkFlow\ApiSdkBuilder\Contracts\SendRequestActionContract;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse as TResponse;
use WrkFlow\ApiSdkBuilder\Testing\Exceptions\TestRequestSentException;

final class SendTestRequestActionAssert implements SendRequestActionContract
{
    private readonly BuildHeadersAction $buildHeadersAction;

    /**
     * @param Closure(StreamInterface|string|OptionsInterface $expectedUri):void|StreamInterface|string|OptionsInterface|null $expectedBody Assert Closure or given expected type.
     * @param array<string, array<string>>                        $expectedHeaders
     */
    public function __construct(
        private readonly string $expectedUri = '',
        private readonly string $expectedMethod = 'GET',
        private readonly Closure|StreamInterface|string|OptionsInterface|array|null $expectedBody = null,
        private readonly array $expectedHeaders = [],
        private readonly ?int $expectedResponseStatusCode = null,
    ) {
        $this->buildHeadersAction = new BuildHeadersAction();
    }

    public function execute(
        ApiInterface $api,
        RequestInterface $request,
        string $responseClass,
        StreamInterface|string|OptionsInterface|null $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        ?ResponseInterface $fakedResponse = null,
        ?Closure $shouldIgnoreLoggersOnError = null,
    ): TResponse {
        Assert::assertEquals(
            expected: 'https://test.localhost' . $this->expectedUri,
            actual: $request->getUri()
                ->__toString(),
            message: 'Expected URI does not match the request URI.'
        );
        Assert::assertEquals(
            expected: $this->expectedMethod,
            actual: $request->getMethod(),
            message: 'Expected method does not match the request method.'
        );

        $environment = $api->environment();

        if ($this->expectedBody === null) {
            Assert::assertNull($body, 'Request body should be null');
        } elseif ($this->expectedBody instanceof OptionsInterface) {
            Assert::assertInstanceOf(OptionsInterface::class, $body, 'Request body should be OptionsContract');
            Assert::assertEquals(
                json_decode($this->expectedBody->toBody($environment), true, 512, JSON_THROW_ON_ERROR),
                json_decode($body->toBody($environment), true, 512, JSON_THROW_ON_ERROR),
                'Request body is different'
            );
        } elseif (is_string($this->expectedBody)) {
            Assert::assertTrue(is_string($body), 'Request body should be string');
            Assert::assertEquals($this->expectedBody, $body, 'Request body is different');
        } elseif ($this->expectedBody instanceof StreamInterface) {
            Assert::assertInstanceOf(StreamInterface::class, $body, 'Request body should be StreamInterface');
            Assert::assertEquals(
                $this->expectedBody->getContents(),
                $body->getContents(),
                'Request body is different'
            );
        } elseif (is_array($this->expectedBody)) {
            Assert::assertInstanceOf(OptionsInterface::class, $body, 'Request body should be OptionsContract');
            Assert::assertEquals(
                $this->expectedBody,
                json_decode($body->toBody($environment), true, 512, JSON_THROW_ON_ERROR),
                'Request body is different'
            );
        } elseif (is_callable($this->expectedBody)) {
            call_user_func($this->expectedBody, $body);
        }

        Assert::assertEquals($this->expectedResponseStatusCode, $expectedResponseStatusCode);
        $actualHeaders = $this->buildHeadersAction->execute($headers, $request)
            ->getHeaders();
        // Remove host header that is added automatically.
        unset($actualHeaders['Host']);
        Assert::assertEquals(
            expected: $this->expectedHeaders,
            actual: $actualHeaders,
            message: 'Expected headers do not match the request headers.'
        );

        throw new TestRequestSentException();
    }
}
