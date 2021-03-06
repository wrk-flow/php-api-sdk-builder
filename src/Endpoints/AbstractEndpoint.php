<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Endpoints;

use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\AbstractApi;
use WrkFlow\ApiSdkBuilder\Response\AbstractResponse;

abstract class AbstractEndpoint
{
    public function __construct(
        protected AbstractApi $api
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

    /**
     * @template T of AbstractResponse
     *
     * @param class-string<T> $class
     *
     * @return T
     */
    protected function makeResponse(string $class, ResponseInterface $response): AbstractResponse
    {
        return $this->api->factory()
            ->container()
            ->makeResponse($class, $response);
    }
}
