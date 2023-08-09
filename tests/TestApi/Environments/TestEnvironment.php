<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\TestApi\Environments;

use JustSteveKing\UriBuilder\Uri;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class TestEnvironment extends AbstractEnvironment
{
    public function uri(): Uri
    {
        return Uri::fromString('http://sdk-builder.test');
    }
}
