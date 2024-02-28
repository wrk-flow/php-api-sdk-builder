<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Loggers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Actions\GetTextForLogAction;
use WrkFlow\ApiSdkBuilder\Log\Contracts\DebugLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerFailConfigEntity;

/**
 * Writes each request/response info to log as debug. Contains request / response body (limited to 10000 characters).
 */
class DebugLogger implements DebugLoggerContract
{
    /**
     * @var int
     */
    final public const MaxContentSize = 10000;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly GetTextForLogAction $getTextForLogAction,
    ) {
    }

    public function requestFailed(RequestFailedEvent $event, LoggerFailConfigEntity $config): void
    {
        $this->debug(
            id: $event->id,
            request: $event->request,
            requestDurationInSeconds: $event->requestDurationInSeconds,
            response: $event->exception->getResponse(),
            exception: $event->exception
        );
    }

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void
    {
        $this->debug(
            id: $event->id,
            request: $event->request,
            requestDurationInSeconds: $event->requestDurationInSeconds,
            exception: $event->exception
        );
    }

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void
    {
        $this->debug(
            id: $event->id,
            request: $event->request,
            requestDurationInSeconds: $event->requestDurationInSeconds,
            response: $event->response->getResponse()
        );
    }

    protected function getBody(?ResponseInterface $response): ?string
    {
        try {
            return $response instanceof ResponseInterface ? substr(
                string: (string) $response->getBody(),
                offset: 0,
                length: self::MaxContentSize
            ) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function debug(
        string $id,
        RequestInterface $request,
        float $requestDurationInSeconds,
        ?ResponseInterface $response = null,
        ?Throwable $exception = null
    ): void {
        $uri = $request->getUri();

        $this->logger->debug(
            message: $this->getTextForLogAction->execute(
                request: $request,
                requestDurationInSeconds: $requestDurationInSeconds,
                response: $response,
                exception: $exception
            ),
            context: array_filter([
                'uri' => (string) $uri,
                'id' => $id,
                'request_headers' => $request->getHeaders(),
                'request_duration' => $requestDurationInSeconds,
                'request_body' => substr(string: (string) $request->getBody(), offset: 0, length: self::MaxContentSize),
                'response_headers' => $response?->getHeaders(),
                'response_body' => $this->getBody($response),
                'exception' => $exception?->getMessage(),
            ])
        );
    }
}
