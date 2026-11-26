<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Http;

use Closure;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\RequestInterface;

final class ResponseBufferMiddleware
{
    public function __construct(
        private readonly TemporaryResponseStorage $storage,
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
            if (! ($options['stream'] ?? false)) {
                $options['sink'] ??= new BufferedResponseStream($this->storage);
            }

            return $handler($request, $options);
        };
    }
}
