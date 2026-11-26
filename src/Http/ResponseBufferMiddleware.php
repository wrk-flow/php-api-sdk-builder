<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Http;

use Closure;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Http\Services\TemporaryResponseStorageService;

final class ResponseBufferMiddleware
{
    public function __construct(
        private readonly TemporaryResponseStorageService $storage,
    )
    {
    }

    public function stack(): HandlerStack
    {
        $stack = HandlerStack::create();
        $stack->push($this);

        return $stack;
    }

    /**
     * @param callable(RequestInterface, array<string, mixed>):PromiseInterface $handler
     * @return Closure(RequestInterface, array<string, mixed>):PromiseInterface
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            // Use the SDK buffer in place of Guzzle's default temp stream so large bodies spill to our directory.
            // Keep caller-provided sinks and leave live streams under the caller's control.
            if ($request->getBody() instanceof BufferedResponseRequestBody && ! ($options['stream'] ?? false)) {
                $options['sink'] ??= new BufferedResponseStream($this->storage);
            }

            return $handler($request, $options);
        };
    }

    public static function forceFor(RequestInterface $request): RequestInterface
    {
        $body = $request->getBody();

        return $body instanceof BufferedResponseRequestBody ? $request : $request->withBody(new BufferedResponseRequestBody($body));
    }
}
