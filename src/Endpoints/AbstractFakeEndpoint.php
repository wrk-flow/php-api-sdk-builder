<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Endpoints;

use Psr\Http\Message\StreamInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeadersAction;
use WrkFlow\ApiSdkBuilder\Entities\EndpointDIEntity;
use WrkFlow\ApiSdkBuilder\Headers\JsonHeaders;
use WrkFlow\ApiSdkBuilder\Headers\XMLHeaders;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\GetValueFactory;

abstract class AbstractFakeEndpoint extends AbstractEndpoint
{
    public function __construct(
        EndpointDIEntity $di,
        protected readonly GetValueFactory $getValueFactory,
        protected readonly BuildHeadersAction $buildHeadersAction
    ) {
        parent::__construct($di);
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
    final protected function makeResponse(
        string $responseClass,
        GetValue|StreamInterface|null $responseBody = null,
        OptionsInterface|StreamInterface|string|null $requestBody = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse {
        $responseHeaders = [];
        $response = $this->factory()
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

                $stream = $this
                    ->factory()
                    ->stream()
                    ->createStream((string) $rawBody->asXML());
            } elseif (is_array($rawBody)) {
                $responseHeaders[] = new JsonHeaders();

                $stream = $this
                    ->factory()
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

        return $this->sendFake(
            response: $response,
            responseClass: $responseClass,
            uri: $this->uri(),
            body: $requestBody,
            headers: array_merge($headers, $responseHeaders),
            expectedResponseStatusCode: $expectedResponseStatusCode
        );
    }
}
