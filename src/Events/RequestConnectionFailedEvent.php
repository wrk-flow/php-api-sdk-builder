<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Events;

use Exception;
use Psr\Http\Message\RequestInterface;

class RequestConnectionFailedEvent
{
    public function __construct(
        public readonly string $id,
        public readonly RequestInterface $request,
        public readonly Exception $exception,
        public readonly float $requestDurationInSeconds,
    ) {
    }
}
