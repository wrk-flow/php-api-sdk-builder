<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;
use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Events\SendingRequestEvent;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

class SendRequestAction
{
    public function __construct(
        private readonly BuildHeadersAction $buildHeadersAction,
        private readonly MakeBodyFromResponseAction $makeBodyFromResponseAction,
    ) {
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param array<int|string,HeadersContract|string|string[]> $headers
     * @param class-string<TResponse>                           $responseClass
     * @param int|null                                          $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     *                                                                                      status code is different
     *
     * @return TResponse
     */
    public function execute(
        AbstractApi $api,
        RequestInterface $request,
        string $responseClass,
        OptionsContract|StreamInterface|string|null $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null
    ): AbstractResponse {
        $dispatcher = $api->factory()
            ->eventDispatcher();
        $timeStart = (float) microtime(true);

        $id = md5($request->getUri() . microtime(true));

        try {
            $dispatcher?->dispatch(new SendingRequestEvent($id, $request, $timeStart));

            $response = $this->sendRequest($api, $request, $body, $headers);
            $container = $api->factory()
                ->container();

            $statusCode = $response->getStatusCode();

            if ($expectedResponseStatusCode === $statusCode ||
                ($expectedResponseStatusCode === null && ($statusCode === 200 || $statusCode === 201))) {
                $body = $this->makeBodyFromResponseAction->execute($responseClass, $response);

                $finalResponse = $container->makeResponse($responseClass, $response, $body);

                $dispatcher?->dispatch(new ResponseReceivedEvent(
                    id: $id,
                    request: $request,
                    response: $finalResponse,
                    requestDurationInSeconds: $this->getRequestDuration($timeStart)
                ));

                return $finalResponse;
            }

            throw $api->createFailedResponseException($statusCode, $response);
        } catch (Exception $exception) {
            if ($exception instanceof ResponseException) {
                $dispatcher?->dispatch(new RequestFailedEvent(
                    id: $id,
                    request: $request,
                    exception: $exception,
                    requestDurationInSeconds: $this->getRequestDuration($timeStart)
                ));
            } else {
                $dispatcher?->dispatch(new RequestConnectionFailedEvent(
                    id: $id,
                    request: $request,
                    exception: $exception,
                    requestDurationInSeconds: $this->getRequestDuration($timeStart)
                ));
            }

            throw $exception;
        }
    }

    /**
     * @param array<int|string,HeadersContract|string|string[]> $headers
     */
    protected function sendRequest(
        AbstractApi $api,
        RequestInterface $request,
        OptionsContract|StreamInterface|string|null $body = null,
        array $headers = []
    ): ResponseInterface {
        $mergedHeaders = array_merge($api->environment()->headers(), $api->headers(), $headers);

        $request = $this->buildHeadersAction->execute($mergedHeaders, $request);
        $request = $this->withBody($api, $body, $request);

        return $api->factory()
            ->client()
            ->sendRequest($request);
    }

    protected function withBody(
        AbstractApi $api,
        OptionsContract|StreamInterface|string|null $body,
        RequestInterface $request
    ): RequestInterface {
        if ($body instanceof StreamInterface) {
            return $request->withBody($body);
        } elseif ($body instanceof OptionsContract) {
            $body = $body->toBody($api->environment());
        }

        if ($body !== null) {
            return $request->withBody($api->factory()->stream()->createStream($body));
        }

        return $request;
    }

    protected function getRequestDuration(float $timeStart): float
    {
        return (float) microtime(true) - $timeStart;
    }
}
