<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Interfaces;

use Closure;
use Throwable;

/**
 * Endpoint names that contains all API endpoint methods that sends a request.
 * This class should be immutable because it is cached.
 *
 * @phpstan-import-type IgnoreLoggersOnExceptionClosure from ApiInterface
 */
interface EndpointInterface
{
    /**
     * Returns a copy of endpoint with ability to prevent loggers from logging failed responses for given
     * exception.
     */
    public function setShouldIgnoreLoggersForExceptions(Closure $closure): static;

    /**
     * Returns a copy of endpoint that will not log to file when the request fails with given exception.
     *
     * @param non-empty-array<class-string<Throwable>> $exceptions
     */
    public function dontReportExceptionsToFile(array $exceptions): static;
}
