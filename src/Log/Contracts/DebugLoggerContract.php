<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Contracts;

use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;

/**
 * Writes each request/response info to log as debug. Contains request / response body (limited to 10000 characters).
 */
interface DebugLoggerContract extends ApiLoggerInterface
{
}
