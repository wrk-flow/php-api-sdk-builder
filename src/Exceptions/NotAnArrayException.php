<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

class NotAnArrayException extends ApiException
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('Response is invalid <%s>', $key));
    }
}
