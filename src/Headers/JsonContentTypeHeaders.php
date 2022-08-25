<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class JsonContentTypeHeaders implements HeadersContract
{
    public function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}
