<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface ApiResponseInterface
{
    public function getResponse(): ResponseInterface;
}
