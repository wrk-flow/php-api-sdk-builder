<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class ServerFailedException extends ResponseException
{
    public function __construct(
        ResponseInterface $response,
        int $statusCode,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        $message ??= 'Server response indicates bad server issue with status code of ' . $response->getStatusCode();

        parent::__construct($response, $message, $previous);
    }
}
