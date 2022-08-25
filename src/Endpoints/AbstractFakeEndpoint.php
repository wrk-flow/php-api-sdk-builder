<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Endpoints;

use Psr\Http\Message\StreamInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Contracts\ApiContract;
use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\GetValueFactory;

abstract class AbstractFakeEndpoint extends AbstractEndpoint
{
    public function __construct(
        ApiContract $api,
        protected readonly GetValueFactory $getValueFactory
    ) {
        parent::__construct($api);
    }

    protected function basePath(): string
    {
        return 'fake';
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse> $responseClass
     *
     * @return TResponse
     */
    protected function makeResponse(
        string $responseClass,
        GetValue|StreamInterface|null $responseBody = null,
        OptionsContract|StreamInterface|string $requestBody = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse {
        $response = $this->api->factory()
            ->response()
            ->createResponse();

        if ($responseBody instanceof StreamInterface) {
            $response = $response->withBody($responseBody);
        } elseif ($responseBody !== null) {
            $rawBody = $responseBody->data->get();
            if ($rawBody instanceof SimpleXMLElement) {
                $response = $response->withBody($this->api->factory()->stream()
                    ->createStream((string) $rawBody->asXML()));
            } elseif (is_array($rawBody)) {
                $response = $response->withBody(
                    $this->api->factory()
                        ->stream()
                        ->createStream((string) json_encode($rawBody, JSON_THROW_ON_ERROR))
                );
            }
        }

        return $this->api->fake(
            $response,
            $responseClass,
            $this->uri(),
            $requestBody,
            $headers,
            $expectedResponseStatusCode
        );
    }
}
