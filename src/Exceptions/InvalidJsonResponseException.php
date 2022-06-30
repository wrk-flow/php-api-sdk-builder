<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

use Psr\Http\Message\ResponseInterface;
use Throwable;

class InvalidJsonResponseException extends ResponseException
{
    /**
     * @param array<string, mixed>|null $json
     */
    public function __construct(
        ResponseInterface $response,
        string $message = '',
        private readonly ?array $json = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($response, $message, $previous);
    }

    public function getJSON(): ?array
    {
        return $this->json;
    }
}
