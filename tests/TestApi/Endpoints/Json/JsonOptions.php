<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Endpoints\Json;

use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;
use WrkFlow\ApiSdkBuilder\Options\AbstractJsonOptions;

final class JsonOptions extends AbstractJsonOptions
{
    /**
     * @param array<int> $keys
     */
    public function __construct(
        public readonly string $input,
        public readonly array $keys
    ) {
    }

    public function toArray(AbstractEnvironment $environment): array
    {
        return array_filter([
            'input' => $this->input,
            'keys' => $this->keys,
        ]);
    }
}
