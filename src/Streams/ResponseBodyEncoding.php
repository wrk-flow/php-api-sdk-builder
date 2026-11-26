<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Streams;

use GuzzleHttp\Psr7\InflateStream;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal
 */
final class ResponseBodyEncoding
{
    private const ContentEncodingHeader = 'Content-Encoding';

    public static function decode(ResponseInterface $response): ResponseInterface
    {
        $encoding = strtolower($response->getHeaderLine(self::ContentEncodingHeader));

        if ($encoding === 'gzip' || $encoding === 'deflate') {
            $stream = $response->getBody();

            // Keep compatibility with transports that supply already-decoded Nyholm bodies.
            if (! ($stream instanceof Stream)) {
                return $response
                    ->withBody(new InflateStream($stream))
                    ->withoutHeader(self::ContentEncodingHeader);
            }
        }

        return $response;
    }
}
