<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Http;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

/**
 * Carries a buffering preference without changing the request bytes or headers.
 * Guzzle can rebuild the request when it applies options, but it keeps the body stream.
 *
 * @internal
 */
final class BufferedResponseRequestBody implements StreamInterface
{
    use StreamDecoratorTrait;

    public function __construct(private StreamInterface $stream)
    {
    }

    public function __toString(): string
    {
        return (string) $this->stream;
    }
}
