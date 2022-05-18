<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Testing\Responses;

use Exception;
use Psr\Http\Message\StreamInterface;
use Stringable;

class StringStream implements StreamInterface, Stringable
{
    protected int $offset = 0;

    public function __construct(public string $string)
    {
    }

    public function __toString(): string
    {
        return $this->string;
    }

    public function rewind(): void
    {
        throw new Exception('Not seekable');
    }

    public function getContents()
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

    public function getSize()
    {
        return null;
    }

    public function isReadable()
    {
        return true;
    }

    public function isWritable()
    {
        return true;
    }

    public function isSeekable()
    {
        return true;
    }

    public function eof()
    {
        return false;
    }

    public function tell()
    {
        return $this->offset;
    }

    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->offset = $offset;
    }

    public function read($length)
    {
        return substr($this->string, $this->offset, $length);
    }

    public function write($string)
    {
        $this->string .= $string;

        return strlen($string);
    }

    public function getMetadata($key = null)
    {
        return null;
    }
}
