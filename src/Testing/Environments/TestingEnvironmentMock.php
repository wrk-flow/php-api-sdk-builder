<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Environments;

use JustSteveKing\UriBuilder\Uri;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class TestingEnvironmentMock extends AbstractEnvironment
{
    public function uri(): Uri
    {
        return Uri::fromString('https://localhost/test');
    }
}
