<?php

declare(strict_types=1);

use WrkFlow\ApiSdkBuilder\Laravel\Configs\ApiSdkConfig;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;

return [
    /**
     * Enable passing requests events to telescopes HTTP watcher.
     */
    ApiSdkConfig::KeyIsTelescopeEnabled => env('API_SDK_IS_TELESCOPE_ENABLED', true),

    ApiSdkConfig::KeyLogging => [
        /**
         * Accepts domain and logger key separated by :
         * Multiple domains can be separated by comma.
         * - Log all: '*:info',
         * - Log only given domain: 'domain.com:debug',
         * - Log multiple domains: 'domain.com:debug,otherdomain.com:info',
         * - Log all except given domain: '*:info,domain.com:'
         */
        ApiSdkConfig::KeyLoggingType => env('API_SDK_LOGGING', '*:info_file'),
        /**
         * List of available loggers key-ed by their name and their implementation.
         *
         * - You can change logger implementation by changing the implementation by overriding the list
         * - You can customize logger implementation by changing concrete class to corresponding interface using
         * container.
         *
         * @link https://larastrict.com/TODO
         */
        ApiSdkConfig::KeyLoggers => LoggerConstants::DefaultLoggersMap,

        /**
         * Base directory name where FileLogger will store log files. Stores in local app storage.
         */
        ApiSdkConfig::KeyLogFileBaseDirectory => env('API_SDK_LOG_FILE_BASE_DIRECTORY', 'requests'),

        /**
         * Base directory name where FileLogger will store log files. Stores in local app storage.
         */
        ApiSdkConfig::KeyKeepLogFilesForDays => env('API_SDK_KEEP_LOG_FILES_FOR_DAYS', 14),

        /**
         * Time of the day when log files will be cleared.
         */
        ApiSdkConfig::KeyTimeForClearLogSchedule => '00:10',
    ],
];
