<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Endpoints;

use Psr\Http\Message\StreamInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeadersAction;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;
use WrkFlow\ApiSdkBuilder\Headers\XMLHeaders;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\GetValueFactory;

abstract class AbstractFakeEndpoint extends AbstractEndpoint
{
    public function __construct(
        ApiInterface $api,
        protected readonly GetValueFactory $getValueFactory,
        protected readonly BuildHeadersAction $buildHeadersAction
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
        OptionsInterface|StreamInterface|string $requestBody = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse {
        $responseHeaders = [];
        $response = $this->api->factory()
            ->response()
            ->createResponse();

        if ($responseBody instanceof StreamInterface) {
            $response = $response->withBody($responseBody);
        } elseif ($responseBody instanceof GetValue) {
            $stream = null;

            $responseHeaders = [];
            $rawBody = $responseBody->data->get();
            if ($rawBody instanceof SimpleXMLElement) {
                $responseHeaders[] = new XMLHeaders();

                $stream = $this->api->factory()
                    ->stream()
                    ->createStream((string) $rawBody->asXML());
            } elseif (is_array($rawBody)) {
                $responseHeaders[] = new JsonHeaders();

                $stream = $this->api->factory()
                    ->stream()
                    ->createStream((string) json_encode($rawBody, JSON_THROW_ON_ERROR));
            }

            if ($stream instanceof StreamInterface) {
                $response = $response->withBody($stream);
            }

            if ($responseHeaders !== []) {
                $response = $this->buildHeadersAction->execute($responseHeaders, $response);
            }
        }

        return $this->api->fake(
            $response,
            $responseClass,
            $this->uri(),
            $requestBody,
            array_merge($headers, $responseHeaders),
            $expectedResponseStatusCode
        );
    }
}
