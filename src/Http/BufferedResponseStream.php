<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Http;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use Throwable;

/**
 * @internal
 */
final class BufferedResponseStream implements StreamInterface
{
    use StreamDecoratorTrait;

    private const SpillAt = 2 * 1024 * 1024;

    private StreamInterface $stream;
    private bool $spilled = false;

    public function __construct(
        private readonly TemporaryResponseStorage $storage,
    )
    {
        $this->stream = Utils::streamFor(Utils::tryFopen('php://memory', 'r+b'));
    }

    public function write(string $string): int
    {
        if (! $this->spilled && $this->stream->tell() + strlen($string) > self::SpillAt) {
            $this->spill();
        }

        return $this->stream->write($string);
    }

    private function spill(): void
    {
        $position = $this->stream->tell();
        $file = $this->storage->createFile();

        try {
            $this->stream->rewind();
            Utils::copyToStream($this->stream, $file);
            $file->seek($position);
        } catch (Throwable $throwable) {
            $file->close();
            $this->stream->seek($position);

            throw $throwable;
        }

        $this->stream->close();
        $this->stream = $file;

        $this->spilled = true;
    }
}
