<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Environments;

use GuzzleHttp\Psr7\Uri;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

class TestingEnvironment extends AbstractEnvironment
{
    public function uri(): Uri
    {
        return new Uri('https://localhost/test');
    }
}
