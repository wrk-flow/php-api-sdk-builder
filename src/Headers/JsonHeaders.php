<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class JsonHeaders implements HeadersContract
{
    public function headers(): array
    {
        return [new JsonContentTypeHeaders(), new AcceptsJsonHeaders()];
    }
}
