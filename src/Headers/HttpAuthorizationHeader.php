<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class HttpAuthorizationHeader implements HeadersInterface
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {
    }

    public function headers(): array
    {
        $key = base64_encode(
            sprintf(
                '%s:%s',
                $this->username,
                $this->password,
            ),
        );

        return [
            'Authorization' => sprintf('Basic %s', $key),
        ];
    }
}
