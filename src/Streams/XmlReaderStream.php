<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Streams;

use GuzzleHttp\Psr7\StreamWrapper;
use Psr\Http\Message\StreamInterface;
use ReflectionMethod;
use WrkFlow\ApiSdkBuilder\Exceptions\ApiSdkRuntimeException;
use XMLReader;

/**
 * Give XMLReader access to a PSR stream. PHP 8.1 to 8.3 need the URI wrapper.
 *
 * @internal
 */
final class XmlReaderStream
{
    private const Scheme = 'apisdkxml';

    /**
     * @var array<string, StreamInterface>
     */
    private static array $pending = [];

    /**
     * @var resource|null
     */
    public $context;

    private StreamInterface $stream;

    public static function open(StreamInterface $stream): XMLReader
    {
        if (! class_exists(XMLReader::class)) {
            throw new ApiSdkRuntimeException('The XMLReader extension is required for streamed SDK XML responses.');
        }

        if (PHP_VERSION_ID >= 80400) {
            // Resolve the PHP 8.4 method at runtime so analysis also works with older XMLReader versions.
            $fromStream = new ReflectionMethod(XMLReader::class, 'fromStream');
            $reader = $fromStream->invoke(null, StreamWrapper::getResource($stream), null, LIBXML_NONET);

            if (! $reader instanceof XMLReader) {
                throw new ApiSdkRuntimeException('Cannot open the SDK XML response.');
            }

            return $reader;
        }

        if (! in_array(self::Scheme, stream_get_wrappers(), true)
            && ! stream_wrapper_register(self::Scheme, self::class)) {
            throw new ApiSdkRuntimeException('Cannot register the SDK XML stream wrapper.');
        }

        $uri = self::Scheme . '://' . bin2hex(random_bytes(16));
        self::$pending[$uri] = $stream;

        try {
            $reader = new XMLReader();

            if (! @$reader->open($uri, null, LIBXML_NONET)) {
                throw new ApiSdkRuntimeException('Cannot open the SDK XML response.');
            }

            return $reader;
        } finally {
            unset(self::$pending[$uri]);
        }
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        if (! array_key_exists($path, self::$pending)) {
            return false;
        }

        $this->stream = self::$pending[$path];

        return true;
    }

    public function stream_read(int $count): string
    {
        return $this->stream->read($count);
    }

    public function stream_eof(): bool
    {
        return $this->stream->eof();
    }

    /**
     * @return array{mode: int}
     */
    public function stream_stat(): array
    {
        return [
            'mode' => 0100444,
        ];
    }

    /**
     * @return array{mode: int}|false
     */
    public function url_stat(string $path, int $flags): array|false
    {
        return array_key_exists($path, self::$pending) ? [
            'mode' => 0100444,
        ] : false;
    }
}
