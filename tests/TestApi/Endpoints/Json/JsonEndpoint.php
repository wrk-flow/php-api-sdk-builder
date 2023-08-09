<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json;

use Exception;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;
use WrkFlow\ApiSdkBuilder\Testing\Responses\JsonResponseMock;
use WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\AbstractTestEndpoint;

class JsonEndpoint extends AbstractTestEndpoint
{
    public function success(): JsonResponse
    {
        $this->client->addResponse(new JsonResponseMock(json: [
            JsonResponse::KeySuccess => true,
        ],));

        return $this->api->get(responseClass: JsonResponse::class, uri: $this->uri(), headers: $this->headers());
    }

    public function failOnStatusCode(int $statusCode): JsonResponse
    {
        $this->client->addResponse(new JsonResponseMock(
            json: [
                JsonResponse::KeySuccess => false,
            ],
            statusCode: $statusCode,
        ));

        return $this->api->get(responseClass: JsonResponse::class, uri: $this->uri(), headers: $this->headers());
    }

    public function failOnServer(): JsonResponse
    {
        $this->client->addException(new Exception('Server failed'));

        return $this->api->get(responseClass: JsonResponse::class, uri: $this->uri(), headers: $this->headers());
    }
    protected function basePath(): string
    {
        return 'json';
    }

    private function headers(): array
    {
        return [new JsonHeaders()];
    }
}
