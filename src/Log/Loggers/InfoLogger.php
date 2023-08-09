<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Loggers;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Contracts\InfoLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;

/**
 * Writes each request/response info to log as info (only request method, status code, host and path).
 */
class InfoLogger implements InfoLoggerContract
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    public function requestFailed(RequestFailedEvent $event, LoggerConfigEntity $config): void
    {
        $this->debug(
            request: $event->request,
            requestDurationInSeconds: $event->requestDurationInSeconds,
            response: $event->exception->getResponse(),
            exception: $event->exception
        );
    }

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void
    {
        $this->debug(
            request: $event->request,
            requestDurationInSeconds: $event->requestDurationInSeconds,
            exception: $event->exception
        );
    }

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void
    {
        $this->debug(
            request: $event->request,
            requestDurationInSeconds: $event->requestDurationInSeconds,
            response: $event->response->getResponse()
        );
    }

    private function debug(
        RequestInterface $request,
        float $requestDurationInSeconds,
        ?ResponseInterface $response = null,
        ?Exception $exception = null
    ): void {
        $uri = $request->getUri();

        $statusCode = $response instanceof ResponseInterface === false
            ? 'FAILED'
            : (string) $response->getStatusCode();

        // GET 200 example.com /api/v1/clients/1 [20s]
        // GET example.com 500 /api/v1/clients/1 [20s]
        // GET example.com FAILED /api/v1/clients/1 [20s]
        $this->logger->info(
            message: sprintf(
                '%s %s %s %s [%ds]',
                $request->getMethod(),
                $statusCode,
                $uri->getHost(),
                $uri->getPath(),
                $requestDurationInSeconds
            ),
            context: $exception instanceof Exception === false ? [] : [
                'exception' => $exception->getMessage(),
            ]
        );
    }
}
