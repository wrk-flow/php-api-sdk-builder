<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\ApiResponseInterface;

abstract class AbstractResponse implements ApiResponseInterface
{
    public function __construct(
        protected readonly ResponseInterface $response
    ) {
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
