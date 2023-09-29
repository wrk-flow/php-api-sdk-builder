<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Contracts;

use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

/**
 * Writes each request/response info to log as info (only request method, status code, host and path).
 */
interface InfoLoggerContract extends ApiLoggerInterface
{
}
