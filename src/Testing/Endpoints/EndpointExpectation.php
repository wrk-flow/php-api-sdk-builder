<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Endpoints;

use Mockery\Matcher\Closure;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\OptionsInterface;

class EndpointExpectation
{
    public function __construct(
        public readonly string $expectedResponseClass,
        public readonly string $expectedUriPath,
        public readonly Closure|StreamInterface|array|string|OptionsInterface|null $assertBody = null,
        public readonly array $expectedHeaders = [],
        public readonly ?int $expectedResponseStatusCode = null,
    ) {
    }
}
