<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ResponseContract
{
    public function getResponse(): ResponseInterface;
}
