<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\ResponseContract;

abstract class AbstractResponse implements ResponseContract
{
    public function __construct(protected readonly ResponseInterface $response)
    {
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
