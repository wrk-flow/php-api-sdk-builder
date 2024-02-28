<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Responses;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ResponseMock implements ResponseInterface
{
    public StreamInterface $body;

    public function __construct(
        string|StreamInterface $body = '',
        public array $headers = [],
        public int $statusCode = 200,
        public string $reasonPhrase = '',
        public string $version = '',
    ) {
        $this->body = is_string($body) ? new StringStream($body) : $body;
    }

    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $this->version = $version;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headers);
    }

    public function getHeader(string $name): array
    {
        return $this->headers[$name];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->headers[$name] ?? []);
    }

    public function withHeader(string $name, $value): MessageInterface
    {
        $this->headers[$name] = [$value];

        return $this;
    }

    public function withAddedHeader(string $name, $value): MessageInterface
    {
        $this->headers[$name][] = $value;

        return $this;
    }

    public function withoutHeader(string $name): MessageInterface
    {
        unset($this->headers[$name]);

        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        $this->body = $body;

        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
