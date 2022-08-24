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

    protected function uri(string $appendPath = ''): Uri
    {
        $uri = $this->api->uri();
        $basePath = $this->appendSlashIfNeeded($this->basePath());
        $appendPath = $this->appendSlashIfNeeded($appendPath);

        return $uri->addPath($uri->path() . $basePath . $appendPath);
    }

    private function appendSlashIfNeeded(string $path): string
    {
        if ($path !== '' && $path[0] !== '/') {
            $path = '/' . $path;
        }

        return $path;
    }
}
