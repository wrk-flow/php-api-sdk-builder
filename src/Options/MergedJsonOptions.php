<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Options;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class MergedJsonOptions extends AbstractJsonOptions
{
    /**
     * @param array<AbstractJsonOptions|null> $options
     */
    public function __construct(
        protected array $options
    ) {
    }

    public function toArray(AbstractEnvironment $environment): array
    {
        $result = [];
        foreach ($this->options as $option) {
            if ($option instanceof AbstractJsonOptions === false) {
                continue;
            }

            $result = array_merge($result, $option->toArray($environment));
        }

        return $result;
    }

    /**
     * @return null[]|AbstractJsonOptions[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
