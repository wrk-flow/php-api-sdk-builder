<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Constants;

use WrkFlow\ApiSdkBuilder\Log\Contracts\DebugLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\FileLoggerContract;
use WrkFlow\ApiSdkBuilder\Log\Contracts\InfoLoggerContract;

class LoggerConstants
{
    final public const DefaultLoggersMap = [
        self::NoLog => [],
        self::Info => [InfoLoggerContract::class],
        self::Debug => [DebugLoggerContract::class],
        self::File => [InfoLoggerContract::class, FileLoggerContract::class],
    ];
    final public const NoLog = '';
    final public const Info = 'info';
    final public const Debug = 'debug';
    final public const File = 'file';
}
