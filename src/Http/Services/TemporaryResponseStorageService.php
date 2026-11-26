<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Http\Services;

use DirectoryIterator;
use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\StreamInterface;
use WrkFlow\ApiSdkBuilder\Exceptions\ApiSdkRuntimeException;

/**
 * Owns disk buffers and removes files that a stopped process left behind.
 */
final class TemporaryResponseStorageService
{
    private const Prefix = 'api-sdk-response-';

    public function __construct(
        private readonly string $directory,
    )
    {
    }

    public function createFile(): StreamInterface
    {
        if (! is_dir($this->directory) && ! @mkdir($this->directory, 0700, true) && ! is_dir($this->directory)) {
            throw new ApiSdkRuntimeException('Cannot create the API SDK response buffer directory.');
        }

        $directory = realpath($this->directory);

        if ($directory === false || ! is_writable($directory)) {
            throw new ApiSdkRuntimeException('The API SDK response buffer directory is not writable.');
        }

        $path = @tempnam($directory, self::Prefix);

        if ($path === false) {
            throw new ApiSdkRuntimeException('Cannot create an API SDK response buffer file.');
        }

        // tempnam can fall back to the system directory. Keep response files in the configured directory.
        if (dirname($path) !== $directory) {
            @unlink($path);

            throw new ApiSdkRuntimeException('Cannot create a response buffer in the configured API SDK directory.');
        }

        $resource = @fopen($path, 'r+b');

        // Hold the lock while the response can still read the file. Cleanup can run in another process.
        if ($resource === false || ! flock($resource, LOCK_EX)) {
            if (is_resource($resource)) {
                fclose($resource);
            }

            @unlink($path);

            throw new ApiSdkRuntimeException('Cannot lock an API SDK response buffer.');
        }

        $stream = new Stream($resource);

        return FnStream::decorate($stream, [
            'close' => static function () use ($stream, $path): void {
                if ($stream->isReadable()) {
                    // Remove the name while the stream still holds its lock.
                    @unlink($path);
                }

                $stream->close();
            },
            'detach' => static function () use ($stream, $path) {
                if ($stream->isReadable()) {
                    @unlink($path);
                }

                return $stream->detach();
            },
        ]);
    }

    public function clearExpired(int $cutoff): int
    {
        if (! is_dir($this->directory)) {
            return 0;
        }

        $deleted = 0;
        foreach (new DirectoryIterator($this->directory) as $entry) {
            if (! str_starts_with($entry->getFilename(), self::Prefix) || $entry->isLink() || ! $entry->isFile()) {
                continue;
            }

            $path = $entry->getPathname();
            clearstatcache(true, $path);
            $modified = @filemtime($path);

            if ($modified === false || $modified >= $cutoff) {
                continue;
            }

            $file = @fopen($path, 'r+b');

            if ($file === false) {
                continue;
            }

            try {
                // A non-blocking lock skips active buffers without delaying the scheduled cleanup.
                if (flock($file, LOCK_EX | LOCK_NB) && @unlink($path)) {
                    $deleted++;
                }
            } finally {
                fclose($file);
            }
        }

        return $deleted;
    }
}
