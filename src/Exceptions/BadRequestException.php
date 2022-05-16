<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class BadRequestException extends ResponseException
{
    public function __construct(ResponseInterface $response, ?string $message = null, ?Throwable $previous = null) {
        $message ??= 'Server response indicates bad request with status code of ' . $response->getStatusCode();
        parent::__construct($response, $message, $previous);
    }
}
