<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Headers;

use WrkFlow\ApiSdkBuilder\Interfaces\HeadersInterface;

class XMLHeaders implements HeadersInterface
{
    public function headers(): array
    {
        return [new XMLContentTypeHeaders(), new AcceptsXMLHeaders()];
    }
}
