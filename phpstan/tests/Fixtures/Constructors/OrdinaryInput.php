<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\PHPStan\Fixtures\Constructors;

use Wrkflow\GetValue\GetValue;

final class OrdinaryInput
{
    public function __construct(
        public readonly GetValue $payload
    ) {
    }
}
