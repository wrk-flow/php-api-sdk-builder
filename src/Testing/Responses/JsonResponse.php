<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Responses;

class JsonResponse extends Response
{
    public function __construct(
        public readonly array $json,
        public array $headers = [],
        public int $statusCode = 200,
        public string $reasonPhrase = '',
        public string $version = '',
    ) {
        parent::__construct(
            json_encode($this->json, JSON_THROW_ON_ERROR),
            $this->headers,
            $this->statusCode,
            $this->reasonPhrase,
            $version,
        );
    }
}
