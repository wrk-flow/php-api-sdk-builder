<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json;

use Exception;
use const true;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use WrkFlow\ApiSdkBuilder\Testing\Responses\JsonResponseMock;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\AbstractTestEndpoint;

class JsonEndpoint extends AbstractTestEndpoint implements JsonEndpointInterface
{
    public function success(): JsonResponse
    {
        $this->expectResponse();

        return $this->sendGet(responseClass: JsonResponse::class, uri: $this->uri(), headers: $this->headers());
    }

    public function store(?OptionsInterface $body = null): JsonResponse
    {
        $this->expectResponse();

        return $this->sendPost(
            responseClass: JsonResponse::class,
            uri: $this->uri(),
            body: $body,
            headers: $this->headers()
        );
    }

    public function failOnStatusCode(int $statusCode): JsonResponse
    {
        $this->expectResponse(status: false, statusCode: $statusCode);

        return $this->sendGet(responseClass: JsonResponse::class, uri: $this->uri(), headers: $this->headers());
    }

    public function failOnServer(): JsonResponse
    {
        $this->client->addException(new Exception('Server failed'));

        return $this->sendGet(responseClass: JsonResponse::class, uri: $this->uri(), headers: $this->headers());
    }

    /**
     * We are checking if our PHPStan rules are working correctly. The error is in phpstan-baseline.neon.
     */
    public function phpStanShouldReportThis(): JsonResponse
    {
        return $this->sendGet(responseClass: AbstractResponse::class, uri: $this->uri(), headers: $this->headers());
    }

    protected function basePath(): string
    {
        return 'json';
    }

    protected function expectResponse(bool $status = true, int $statusCode = 200): void
    {
        $this->client->addResponse(new JsonResponseMock(
            json: [
                JsonResponse::KeySuccess => $status,
            ],
            statusCode: $statusCode,
        ));
    }

    private function headers(): array
    {
        return [new JsonHeaders()];
    }
}
