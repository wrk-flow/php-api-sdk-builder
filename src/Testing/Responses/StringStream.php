<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Responses;

use Exception;
use Psr\Http\Message\StreamInterface;
use Stringable;

class StringStream implements StreamInterface, Stringable
{
    protected int $offset = 0;

    public function __construct(
        public string $string
    ) {
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function rewind(): void
    {
        throw new Exception('Not seekable');
    }

    public function getContents(): string
    {
        return $this->__toString();
    }

    public function close(): void
    {
    }

    public function detach()
    {
        return null;
    }

    public function getSize(): ?int
    {
        return null;
    }

    public function isReadable(): bool
    {
        return true;
    }

    public function isWritable(): bool
    {
        return true;
    }

    public function isSeekable(): bool
    {
        return true;
    }

    public function eof(): bool
    {
        return false;
    }

    public function tell(): int
    {
        return $this->offset;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->offset = $offset;
    }

    public function read($length): string
    {
        return substr($this->string, $this->offset, $length);
    }

    public function write($string): int
    {
        $this->string .= $string;

        return strlen($string);
    }

    public function getMetadata($key = null)
    {
        return null;
    }
}
