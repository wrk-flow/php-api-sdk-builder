<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing;

use JustSteveKing\UriBuilder\Uri;
use Mockery;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ApiContract;
use WrkFlow\ApiSdkBuilder\Contracts\ApiFactoryContract;
use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use WrkFlow\ApiSdkBuilder\Testing\Endpoints\EndpointExpectation;
use WrkFlow\ApiSdkBuilder\Testing\Environments\TestingEnvironmentMock;
use WrkFlow\ApiSdkBuilder\Testing\Factories\ApiFactoryMock;

class ApiMock implements ApiContract
{
    public array $postExpectations = [];

    public array $getExpectations = [];

    public array $putExpectations = [];

    public array $deleteExpectations = [];

    public readonly TestingEnvironmentMock $environment;

    public function __construct()
    {
        $this->environment = new TestingEnvironmentMock();
    }

    public function environment(): AbstractEnvironment
    {
        return $this->environment;
    }

    public function factory(): ApiFactoryContract
    {
        return new ApiFactoryMock();
    }

    public function uri(): Uri
    {
        return $this->environment()
            ->uri();
    }

    public function get(
        string $responseClass,
        Uri $uri,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $this->getExpectations = $this->assertRequest(
            $this->getExpectations,
            $responseClass,
            $uri,
            null,
            $headers,
            $expectedResponseStatusCode
        );

        return $this->returnResponseMock($responseClass);
    }

    public function post(
        string $responseClass,
        Uri $uri,
        StreamInterface|string|OptionsContract $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $this->postExpectations = $this->assertRequest(
            $this->postExpectations,
            $responseClass,
            $uri,
            $body,
            $headers,
            $expectedResponseStatusCode
        );

        return $this->returnResponseMock($responseClass);
    }

    public function put(
        string $responseClass,
        Uri $uri,
        StreamInterface|string|OptionsContract $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $this->putExpectations = $this->assertRequest(
            $this->putExpectations,
            $responseClass,
            $uri,
            $body,
            $headers,
            $expectedResponseStatusCode
        );

        return $this->returnResponseMock($responseClass);
    }

    public function delete(
        string $responseClass,
        Uri $uri,
        StreamInterface|string|OptionsContract $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): AbstractResponse {
        $this->deleteExpectations = $this->assertRequest(
            $this->deleteExpectations,
            $responseClass,
            $uri,
            $body,
            $headers,
            $expectedResponseStatusCode
        );
        return $this->returnResponseMock($responseClass);
    }

    public function createFailedResponseException(int $statusCode, ResponseInterface $response): ResponseException
    {
        return new ResponseException($response, 'Error code ' . $statusCode);
    }

    public function headers(): array
    {
        return [
            'X-Testing-Header' => 'true',
        ];
    }

    /**
     * @param array<EndpointExpectation> $expectations
     */
    protected function assertRequest(
        array $expectations,
        string $responseClass,
        Uri $uri,
        StreamInterface|string|OptionsContract $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
    ): array {
        $expectation = array_shift($expectations);

        Assert::assertNotNull(
            $expectation,
            'Request expectation missing - endpoint is probably using different HTTP/s method'
        );

        $environment = $this->environment();
        $baseUri = $environment->uri();
        $expectedUrl = $baseUri->addPath($baseUri->path() . $expectation->expectedUriPath)->toString();
        Assert::assertEquals($expectedUrl, $uri->toString());

        Assert::assertEquals($expectation->expectedResponseClass, $responseClass);
        Assert::assertEquals($expectation->expectedHeaders, $headers);
        Assert::assertEquals($expectation->expectedResponseStatusCode, $expectedResponseStatusCode);

        if ($expectation->assertBody === null) {
            Assert::assertNull($body, 'Request body should be null');
        } elseif ($expectation->assertBody instanceof OptionsContract) {
            Assert::assertInstanceOf(OptionsContract::class, $body, 'Request body should be OptionsContract');
            Assert::assertEquals(
                json_decode($expectation->assertBody->toBody($environment), true, 512, JSON_THROW_ON_ERROR),
                json_decode($body->toBody($environment), true, 512, JSON_THROW_ON_ERROR),
                'Request body is different'
            );
        } elseif (is_string($expectation->assertBody)) {
            Assert::assertTrue(is_string($body), 'Request body should be string');
            Assert::assertEquals($expectation->assertBody, $body, 'Request body is different');
        } elseif ($expectation->assertBody instanceof StreamInterface) {
            Assert::assertInstanceOf(StreamInterface::class, $body, 'Request body should be StreamInterface');
            Assert::assertEquals(
                $expectation->assertBody->getContents(),
                $body->getContents(),
                'Request body is different'
            );
        } elseif (is_array($expectation->assertBody)) {
            Assert::assertInstanceOf(OptionsContract::class, $body, 'Request body should be OptionsContract');
            Assert::assertEquals(
                $expectation->assertBody,
                json_decode($body->toBody($environment), true, 512, JSON_THROW_ON_ERROR),
                'Request body is different'
            );
        } elseif (is_callable($expectation->assertBody)) {
            call_user_func($expectation->assertBody, $body);
        }

        return $expectations;
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse> $responseClass
     *
     * @return TResponse
     */
    protected function returnResponseMock(string $responseClass): AbstractResponse
    {
        /** @var TResponse $mock */
        $mock = Mockery::mock($responseClass);
        return $mock;
    }
}
