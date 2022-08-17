<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Responses;

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

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function withProtocolVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function hasHeader($name)
    {
        return array_key_exists($name, $this->headers);
    }

    public function getHeader($name)
    {
        return $this->headers[$name];
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->headers[$name] ?? []);
    }

    public function withHeader($name, $value)
    {
        $this->headers[$name] = [$value];

        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        $this->headers[$name][] = $value;

        return $this;
    }

    public function withoutHeader($name)
    {
        unset($this->headers[$name]);

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body)
    {
        $this->body = $body;

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function withStatus($code, $reasonPhrase = '')
    {
        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}
