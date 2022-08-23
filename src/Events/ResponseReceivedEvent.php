<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Events;

use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Responses\AbstractResponse;

class ResponseReceivedEvent
{
    public function __construct(
        public readonly string $id,
        public readonly RequestInterface $request,
        public readonly AbstractResponse $response,
        public readonly float $requestDurationInSeconds,
    ) {
    }
}
