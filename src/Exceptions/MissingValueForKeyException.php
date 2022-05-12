<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

class MissingValueForKeyException extends ResponseException
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('Response is missing a value for a key <%s>', $key));
    }
}
