<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Endpoints;

use JustSteveKing\UriBuilder\Uri;
use WrkFlow\ApiSdkBuilder\Contracts\ApiContract;

abstract class AbstractEndpoint
{
    public function __construct(
        protected ApiContract $api,
    ) {
    }

    /**
     * Appends to base path in uri. Must start with /.
     */
    abstract protected function basePath(): string;

    protected function uri(): Uri
    {
        $uri = $this->api->uri();
        $basePath = $this->basePath();

        if ($basePath !== '' && $basePath[0] !== '/') {
            $basePath = '/' . $basePath;
        }

        return $uri->addPath($uri->path() . $basePath);
    }
}
