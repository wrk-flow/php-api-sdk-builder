<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class BearerTokenAuthorizationHeader implements HeadersContract
{
    public function __construct(public readonly string $token)
    {
    }

    public function headers(): array
    {
        return [
            'Authorization' => sprintf('Bearer %s', $this->token),
        ];
    }
}
