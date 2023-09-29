<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Exceptions;

use Exception;
use Psr\Http\Client\ClientExceptionInterface;

class ApiException extends Exception implements ClientExceptionInterface
{
}
