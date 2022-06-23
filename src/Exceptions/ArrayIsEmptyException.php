<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

class ArrayIsEmptyException extends ApiException
{
    public function __construct(string $key)
    {
        parent::__construct(sprintf('Response returned an empty array <%s>', $key));
    }
}
