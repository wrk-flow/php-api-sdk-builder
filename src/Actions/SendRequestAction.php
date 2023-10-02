<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Closure;
use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SendRequestActionContract;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Events\SendingRequestEvent;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\EnvironmentFakeResponseInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerFailConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

/**
 * @phpstan-import-type IgnoreLoggersOnExceptionClosure from ApiInterface
 */
final class SendRequestAction implements SendRequestActionContract
{
    public function __construct(
        private readonly BuildHeadersAction $buildHeadersAction,
        private readonly MakeBodyFromResponseAction $makeBodyFromResponseAction,
        private readonly GetLoggerAction $getLoggerAction,
    ) {
    }

    public function execute(
        ApiInterface $api,
        RequestInterface $request,
        string $responseClass,
        OptionsInterface|StreamInterface|string|null $body = null,
        array $headers = [],
        ?int $expectedResponseStatusCode = null,
        ?ResponseInterface $fakedResponse = null,
        Closure $shouldIgnoreLoggersOnError = null,
    ): AbstractResponse {
        $timeStart = (float) microtime(true);

        $id = md5($request->getUri() . microtime(true));

        $dispatcher = $api
            ->factory()
            ->eventDispatcher();

        $loggerConfig = $api->factory()
            ->loggerConfig();

        $logger = $this->getLoggerAction->execute(config: $loggerConfig, host: $request->getUri()->getHost());

        $environment = $api->environment();

        $request = $this->buildRequest($environment, $api, $headers, $request, $body);

        $response = $this->sendRequest(
            environment: $environment,
            api: $api,
            dispatcher: $dispatcher,
            logger: $logger,
            loggerConfig: $loggerConfig,
            request: $request,
            responseClass: $responseClass,
            requestId: $id,
            timeStart: $timeStart,
            fakedResponse: $fakedResponse,
        );

        return $this->handleResponse(
            api: $api,
            response: $response,
            expectedResponseStatusCode: $expectedResponseStatusCode,
            responseClass: $responseClass,
            dispatcher: $dispatcher,
            logger: $logger,
            loggerConfig: $loggerConfig,
            id: $id,
            request: $request,
            timeStart: $timeStart,
            shouldIgnoreLoggersOnError: $shouldIgnoreLoggersOnError,
        );
    }

    /**
     * @param class-string<AbstractResponse> $responseClass
     */
    private function sendRequest(
        AbstractEnvironment $environment,
        ApiInterface $api,
        ?EventDispatcherInterface $dispatcher,
        ?ApiLoggerInterface $logger,
        LoggerConfigEntity $loggerConfig,
        RequestInterface $request,
        string $responseClass,
        string $requestId,
        float $timeStart,
        ?ResponseInterface $fakedResponse = null,
    ): ResponseInterface {
        try {
            $dispatcher?->dispatch(new SendingRequestEvent($requestId, $request, $timeStart));

            if ($fakedResponse instanceof ResponseInterface) {
                return $fakedResponse;
            } elseif ($environment instanceof EnvironmentFakeResponseInterface) {
                $response = $environment->getResponse($request, $responseClass, $api->factory());
                if ($response instanceof ResponseInterface) {
                    return $response;
                }
            }

            return $api->factory()
                ->client()
                ->sendRequest($request);
        } catch (Exception $exception) {
            $this->rethrowIfPHPUnitException($exception);

            $event = new RequestConnectionFailedEvent(
                id: $requestId,
                request: $request,
                exception: $exception,
                requestDurationInSeconds: $this->getRequestDuration($timeStart)
            );
            $dispatcher?->dispatch($event);
            $logger?->requestConnectionFailed(event: $event, config: $loggerConfig);

            throw $exception;
        }
    }

    private function withBody(
        ApiInterface $api,
        OptionsInterface|StreamInterface|string|null $body,
        RequestInterface $request
    ): RequestInterface {
        if ($body instanceof StreamInterface) {
            return $request->withBody($body);
        } elseif ($body instanceof OptionsInterface) {
            $body = $body->toBody($api->environment());
        }

        if ($body !== null) {
            return $request->withBody($api->factory()->stream()->createStream($body));
        }

        return $request;
    }

    private function getRequestDuration(float $timeStart): float
    {
        return (float) microtime(true) - $timeStart;
    }

    /**
     * @template TResponse of AbstractResponse
     *
     * @param class-string<TResponse>         $responseClass
     * @param int|null                        $expectedResponseStatusCode Will raise and failed
     *                                                                                      exception if response
     *                                                                                      status code is different
     * @param IgnoreLoggersOnExceptionClosure $shouldIgnoreLoggersOnError
     *
     * @return TResponse
     */
    private function handleResponse(
        ApiInterface $api,
        ResponseInterface $response,
        ?int $expectedResponseStatusCode,
        string $responseClass,
        ?EventDispatcherInterface $dispatcher,
        ?ApiLoggerInterface $logger,
        LoggerConfigEntity $loggerConfig,
        string $id,
        RequestInterface $request,
        float $timeStart,
        Closure $shouldIgnoreLoggersOnError = null,
    ): mixed {
        try {
            $container = $api->factory()
                ->container();

            $statusCode = $response->getStatusCode();

            if ($expectedResponseStatusCode === $statusCode ||
                ($expectedResponseStatusCode === null && ($statusCode === 200 || $statusCode === 201))) {
                $body = $this->makeBodyFromResponseAction->execute($responseClass, $response);

                $finalResponse = $container->makeResponse($responseClass, $response, $body);

                $event = new ResponseReceivedEvent(
                    id: $id,
                    request: $request,
                    response: $finalResponse,
                    requestDurationInSeconds: $this->getRequestDuration($timeStart)
                );
                $dispatcher?->dispatch($event);
                $logger?->responseReceivedEvent(event: $event, config: $loggerConfig);

                return $finalResponse;
            }

            throw $api->createFailedResponseException($statusCode, $response);
        } catch (Exception $exception) {
            $this->rethrowIfPHPUnitException($exception);

            // Wrap any custom exception to response exception because we have a response
            if ($exception instanceof ResponseException === false) {
                $exception = new ResponseException($response, $exception->getMessage(), $exception);
            }

            if ($shouldIgnoreLoggersOnError instanceof Closure) {
                $ignoreLoggers = $shouldIgnoreLoggersOnError($exception);
            }

            $event = new RequestFailedEvent(
                id: $id,
                request: $request,
                exception: $exception,
                requestDurationInSeconds: $this->getRequestDuration($timeStart)
            );
            $dispatcher?->dispatch($event);
            $failLogConfig = new LoggerFailConfigEntity(
                config: $loggerConfig,
                ignoreLoggersOnFail: $ignoreLoggers ?? []
            );

            if ($logger instanceof ApiLoggerInterface && $failLogConfig->shouldIgnoreLogger($logger) === false) {
                $logger->requestFailed(event: $event, config: $failLogConfig);
            }

            throw $exception;
        }
    }

    private function buildRequest(
        AbstractEnvironment $environment,
        ApiInterface $api,
        array $headers,
        RequestInterface $request,
        StreamInterface|string|OptionsInterface|null $body
    ): RequestInterface {
        $mergedHeaders = array_merge($environment->headers(), $api->headers(), $headers);

        $request = $this->buildHeadersAction->execute($mergedHeaders, $request);
        return $this->withBody($api, $body, $request);
    }

    /**
     * If we are using assert mocking, we do not want to log the exception and
     * rethrow it.
     */
    private function rethrowIfPHPUnitException(Exception $exception): void
    {
        // PHPUnit can be missing.
        $class = '\\' . \PHPUnit\Exception::class;
        if ($exception instanceof $class) {
            throw $exception;
        }
    }
}
