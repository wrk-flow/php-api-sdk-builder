<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Laravel\Configs;

use Illuminate\Config\Repository;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\GetValueFactory;

/**
 * Taken from LaraStrict
 */
abstract class AbstractConfig
{
    protected readonly GetValue $config;
    private readonly string $configFileName;

    public function __construct(
        Repository $config,
        private readonly GetValueFactory $getValueFactory,
    ) {
        $this->configFileName = $this->getConfigFileName();
        $data = $config->get($this->configFileName, []);
        assert(is_array($data));

        $this->config = $this->getValueFactory->array($data);
    }

    abstract protected function getConfigFileName(): string;
}
