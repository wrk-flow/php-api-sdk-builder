<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Environments;

use JustSteveKing\UriBuilder\Uri;
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

abstract class AbstractEnvironment implements HeadersContract
{
    /**
     * @param array<string|int, string|HeadersContract|string[]> $headers
     */
    public function __construct(
        protected array $headers = [],
    ) {
    }

    abstract public function uri(): Uri;

    public function headers(): array
    {
        return $this->headers;
    }
}
