<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
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
        $timeStart = (float) microtime(true);

        $id = md5($request->getUri() . microtime(true));

        $dispatcher = $api
            ->factory()
            ->eventDispatcher();

        $response = $this->sendRequest($id, $timeStart, $dispatcher, $api, $request, $body, $headers);

        return $this->handleResponse(
            api: $api,
            response: $response,
            expectedResponseStatusCode: $expectedResponseStatusCode,
            responseClass: $responseClass,
            dispatcher: $dispatcher,
            id: $id,
            request: $request,
            timeStart: $timeStart
        );
    }

    /**
     * @param array<int|string,HeadersContract|string|string[]> $headers
     */
    protected function sendRequest(
        string $requestId,
        float $timeStart,
        ?EventDispatcherInterface $dispatcher,
        AbstractApi $api,
        RequestInterface $request,
        OptionsContract|StreamInterface|string|null $body = null,
        array $headers = []
    ): ResponseInterface {
        $dispatcher?->dispatch(new SendingRequestEvent($requestId, $request, $timeStart));

        try {
            $mergedHeaders = array_merge($api->environment()->headers(), $api->headers(), $headers);

            $request = $this->buildHeadersAction->execute($mergedHeaders, $request);
            $request = $this->withBody($api, $body, $request);

            return $api->factory()
                ->client()
                ->sendRequest($request);
        } catch (Exception $exception) {
            $dispatcher?->dispatch(new RequestConnectionFailedEvent(
                id: $requestId,
                request: $request,
                exception: $exception,
                requestDurationInSeconds: $this->getRequestDuration($timeStart)
            ));

            throw $exception;
        }
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

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse> $responseClass
     * @param int|null                $expectedResponseStatusCode                           Will raise and failed
     *                                                                                      exception if response
     *                                                                                      status code is different
     *
     * @return TResponse
     */
    protected function handleResponse(
        AbstractApi $api,
        ResponseInterface $response,
        ?int $expectedResponseStatusCode,
        string $responseClass,
        ?EventDispatcherInterface $dispatcher,
        string $id,
        RequestInterface $request,
        float $timeStart
    ): mixed {
        try {
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
            // Wrap any custom exception to response exception because we have a response
            if ($exception instanceof ResponseException === false) {
                $exception = new ResponseException($response, $exception->getMessage(), $exception);
            }

            $dispatcher?->dispatch(new RequestFailedEvent(
                id: $id,
                request: $request,
                exception: $exception,
                requestDurationInSeconds: $this->getRequestDuration($timeStart)
            ));

            throw $exception;
        }
    }
}
