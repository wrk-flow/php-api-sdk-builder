<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder;

use JustSteveKing\UriBuilder\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Actions\BuildHeaders;
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;
use WrkFlow\ApiSdkBuilder\Contracts\OptionsContract;
use WrkFlow\ApiSdkBuilder\Endpoints\AbstractEndpoint;
use WrkFlow\ApiSdkBuilder\Environments\AbstractEnvironment;

abstract class AbstractApi implements HeadersContract
{
    private readonly BuildHeaders $buildHeaders;

    public function __construct(
        public readonly AbstractEnvironment $environment,
        private readonly ApiFactory $factory,
    ) {
        $this->buildHeaders = $this->factory()
            ->container()
            ->make(BuildHeaders::class);
    }

    public function factory(): ApiFactory
    {
        // Makes the factory testable
        return $this->factory;
    }

    public function uri(): Uri
    {
        return $this->environment->uri();
    }

    /**
     * @param array<HeadersContract> $headers
     */
    public function get(Uri $uri, array $headers = []): ResponseInterface
    {
        $request = $this->factory()
            ->request()
            ->createRequest('GET', $uri->toString());

        return $this->sendRequest($request, $headers);
    }

    /**
     * @param array<int|string,HeadersContract|string|string[]> $headers
     */
    public function post(
        Uri $uri,
        OptionsContract|StreamInterface|string $body = null,
        array $headers = []
    ): ResponseInterface {
        $request = $this->factory()
            ->request()
            ->createRequest('POST', $uri->toString());

        return $this->sendRequest($request, $headers, $body);
    }

    /**
     * @param array<int|string,HeadersContract|string|string[]> $headers
     */
    protected function sendRequest(
        RequestInterface $request,
        array $headers = [],
        OptionsContract|StreamInterface|string|null $body = null
    ): ResponseInterface {
        $mergedHeaders = array_merge($this->environment->headers(), $this->headers(), $headers);

        $request = $this->buildHeaders->execute($mergedHeaders, $request);
        $request = $this->withBody($body, $request);

        return $this->factory()
            ->client()
            ->sendRequest($request);
    }

    /**
     * @template T of AbstractEndpoint
     * @param class-string<T> $endpoint
     *
     * @return T
     */
    protected function makeEndpoint(string $endpoint): AbstractEndpoint
    {
        return $this->factory()
            ->container()
            ->makeEndpoint($this, $endpoint);
    }

    protected function withBody(
        OptionsContract|StreamInterface|string|null $body,
        RequestInterface $request
    ): RequestInterface {
        if ($body instanceof StreamInterface) {
            return $request->withBody($body);
        } elseif ($body instanceof OptionsContract) {
            $body = $body->toBody();
        }

        if ($body !== null) {
            return $request->withBody($this->factory()->stream()->createStream($body));
        }

        return $request;
    }
}
