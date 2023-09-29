<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class JsonContentTypeHeaders implements HeadersInterface
{
    public function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }
}
