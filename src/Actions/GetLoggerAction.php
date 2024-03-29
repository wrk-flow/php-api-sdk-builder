<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\LoggerConfigEntity;
use WrkFlow\ApiSdkBuilder\Log\Interfaces\ApiLoggerInterface;
use WrkFlow\ApiSdkBuilder\Log\Loggers\StackLogger;

/**
 * This action is responsible for getting the logger from the logger config.
 * - We expect, that container should make Loggers singletons (for performance reasons while mixing multiple APIs).
 * - We expect that cache holds in AbstractApi lifetime (SendRequestAction is created in API constructor
 * GetLoggerAction is its own dependency - cache will live between requests).
 */
class GetLoggerAction
{
    private array $cache = [];

    public function __construct(
        private readonly SDKContainerFactoryContract $container,
    ) {
    }

    public function execute(LoggerConfigEntity $config, string $host): ?ApiLoggerInterface
    {
        $loggerKey = $config->loggerByHost[$host] ?? $config->logger;

        if (array_key_exists($loggerKey, $config->loggersMap->loggers) === false) {
            return null;
        }

        if (array_key_exists($loggerKey, $this->cache)) {
            return $this->cache[$loggerKey];
        }

        $logger = $this->getLogger(config: $config, loggerKey: $loggerKey);

        $this->cache[$loggerKey] = $logger;

        return $logger;
    }

    protected function getLogger(LoggerConfigEntity $config, mixed $loggerKey): ?ApiLoggerInterface
    {
        $loggers = $config->loggersMap->loggers[$loggerKey];

        if ($loggers === []) {
            return null;
        }

        if (count($loggers) === 1) {
            return $this->makeLogger($loggers[0]);
        }

        return new StackLogger(
            loggers: array_map(
                callback: fn (string $logger): ApiLoggerInterface => $this->makeLogger($logger),
                array: $loggers
            )
        );
    }

    /**
     * @param class-string<ApiLoggerInterface> $class
     */
    protected function makeLogger(string $class): ApiLoggerInterface
    {
        $result = $this->container->make($class);
        assert($result instanceof ApiLoggerInterface);
        return $result;
    }
}
