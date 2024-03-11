<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Environments;

use Psr\Http\Message\UriInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

abstract class AbstractEnvironment implements HeadersInterface
{
    /**
     * @param array<string|int, string|HeadersInterface|string[]> $headers
     */
    public function __construct(
        protected array $headers = [],
    ) {
    }

    abstract public function uri(): UriInterface;

    public function headers(): array
    {
        return $this->headers;
    }

    public function addHeader(array|HeadersInterface $header): self
    {
        $this->headers[] = $header;

        return $this;
    }
}
