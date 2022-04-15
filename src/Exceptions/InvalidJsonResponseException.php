<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

use Exception;
use Throwable;

class InvalidJsonResponseException extends Exception
{
    /**
     * @param array<string, mixed>|null  $response
     */
    public function __construct(
        string $message = '',
        private readonly ?array $response = null,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }
}
