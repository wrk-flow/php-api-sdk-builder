<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Events;

use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Exceptions\ResponseException;

class RequestFailedEvent
{
    public function __construct(
        public readonly string $id,
        public readonly RequestInterface $request,
        public readonly ResponseException $exception,
        public readonly float $requestDurationInSeconds,
    ) {
    }
}
