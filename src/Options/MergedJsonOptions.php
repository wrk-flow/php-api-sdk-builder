<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Options;

class MergedJsonOptions extends AbstractJsonOptions
{
    /**
     * @param array<AbstractJsonOptions|null> $options
     */
    public function __construct(
        protected array $options
    ) {
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->options as $option) {
            if ($option === null) {
                continue;
            }

            $result = array_merge($result, $option->toArray());
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
