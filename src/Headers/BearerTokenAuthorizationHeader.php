<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class BearerTokenAuthorizationHeader implements HeadersInterface
{
    public function __construct(
        public readonly string $token
    ) {
    }

    public function headers(): array
    {
        return [
            'Authorization' => sprintf('Bearer %s', $this->token),
        ];
    }
}
