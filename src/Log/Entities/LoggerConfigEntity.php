<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Entities;

use LogicException;
use WrkFlow\ApiSdkBuilder\Log\Constants\LoggerConstants;

class LoggerConfigEntity
{
    public readonly string $logger;
    /**
     * @var array<string, string>
     */
    public readonly array $loggerByHost;

    /**
     * @param string                $logger Accepts domain and logger key separated by :. Multiple domains can be
     *     separated by comma. Log all: '*:info', Log only given domain: 'domain.com:debug', Log multiple
     *     domains: 'domain.com:debug,otherdomain.com:info' Log all except given domain: '*:info,domain.com:'
     */
    public function __construct(
        string $logger = '',
        public readonly LoggersMapEntity $loggersMap = new LoggersMapEntity(LoggerConstants::DefaultLoggersMap),
        public readonly string $fileBaseDir = 'requests',
        public readonly int $keepLogFilesForDays = 14,
    ) {
        $defaultLogger = '';
        $loggerByHost = [];
        foreach (explode(',', $logger) as $item) {
            $settings = explode(':', $item);

            $count = count($settings);
            if ($count === 1) {
                $host = '*';
                $logger = $settings[0];
            } elseif ($count === 2) {
                [$host, $logger] = $settings;
            } else {
                throw new LogicException(
                    'Invalid logging settings. Expected format: "host:logger" or "logger". Got ' . $item
                );
            }

            if ($host === '*') {
                $defaultLogger = $logger;
                continue;
            }

            $loggerByHost[$host] = $logger;
        }

        $this->logger = $defaultLogger;
        $this->loggerByHost = $loggerByHost;
    }
}
