<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Loggers;

use Exception;
use GuzzleHttp\Psr7\StreamWrapper;
use League\Flysystem\FilesystemOperator;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use WrkFlow\ApiSdkBuilder\Events\RequestConnectionFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\RequestFailedEvent;
use WrkFlow\ApiSdkBuilder\Events\ResponseReceivedEvent;
use WrkFlow\ApiSdkBuilder\Log\Actions\GetExtensionFromContentTypeAction;
use WrkFlow\ApiSdkBuilder\Log\Contracts\BuildRequestLogFileActionContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLogPathServiceContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerFailConfigEntity;

/**
 * Writes each request and response to a file:
 * - request: requests/{host}/{Y-m-d}/{date-time}{path}.{http}
 * - response: {base-path}.{extension|json|xml|txt}
 */
class FileLogger implements FileLoggerContract
{
    public function __construct(
        private readonly FilesystemOperator $filesystemOperator,
        private readonly LoggerInterface $logger,
        private readonly FileLogPathServiceContract $fileLogPathService,
        private readonly GetExtensionFromContentTypeAction $getExtensionFromContentTypeAction,
        private readonly BuildRequestLogFileActionContract $buildRequestLogFileAction,
    ) {
    }

    public function requestFailed(RequestFailedEvent $event, LoggerFailConfigEntity $config): void
    {
        $this->log(
            config: $config->config,
            id: $event->id,
            request: $event->request,
            type: 'failed',
            response: $event->exception->getResponse(),
        );
    }

    public function requestConnectionFailed(RequestConnectionFailedEvent $event, LoggerConfigEntity $config): void
    {
        $this->log(config: $config, id: $event->id, request: $event->request, type: 'connection-failed');
    }

    public function responseReceivedEvent(ResponseReceivedEvent $event, LoggerConfigEntity $config): void
    {
        $this->log(
            config: $config,
            id: $event->id,
            request: $event->request,
            response: $event->response->getResponse(),
        );
    }

    private function log(
        LoggerConfigEntity $config,
        string $id,
        RequestInterface $request,
        ?string $type = null,
        ?ResponseInterface $response = null,
    ): void {
        try {
            $basePath = $this->fileLogPathService->getFilePath(
                baseDir: $config->fileBaseDir,
                request: $request,
                id: $id,
                type: $type,
            );

            $requestFile = $this->buildRequestLogFileAction->execute(request: $request, basePath: $basePath);
            $this->filesystemOperator->write(location: $requestFile->filePath, contents: $requestFile->contents);

            if ($response instanceof ResponseInterface === false) {
                return;
            }

            $extension = $this->getExtensionFromContentTypeAction->execute(
                contentType: $response->getHeaderLine('Content-Type'),
            );

            $responseFilePath = $basePath . '.' . $extension;
            $body = $response->getBody();

            if (! $body->isSeekable()) {
                $this->filesystemOperator->write(location: $responseFilePath, contents: (string) $body);

                return;
            }

            $position = $body->tell();
            $body->rewind();
            $resource = StreamWrapper::getResource($body);

            try {
                $this->filesystemOperator->writeStream($responseFilePath, $resource);
            } finally {
                fclose($resource);
                $body->seek($position);
            }
        } catch (Exception $exception) {
            $this->logger->error('Failed to write request to file', [
                'exception' => $exception,
            ]);
        }
    }
}
