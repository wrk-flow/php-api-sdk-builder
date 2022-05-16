<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class ResponseException extends ApiException
{
    public function __construct(
        private readonly ResponseInterface $response,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $response->getStatusCode(), $previous);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
