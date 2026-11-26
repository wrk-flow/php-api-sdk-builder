<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use DOMDocument;
use DOMElement;
use Generator;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Exceptions\ApiSdkRuntimeException;
use WrkFlow\ApiSdkBuilder\Streams\ResponseBodyEncoding;
use WrkFlow\ApiSdkBuilder\Streams\XmlReaderStream;
use Wrkflow\GetValue\DataHolders\XMLData;
use Wrkflow\GetValue\GetValue;
use XMLReader;

final class StreamedXmlBody
{
    public function __construct(
        private readonly ResponseInterface $response,
    )
    {
    }

    /**
     * Validate the document before records are consumed and keep only the named root children.
     *
     * @param list<string> $names
     */
    public function readHeader(array $names): GetValue
    {
        $header = '<Response>';
        $accept = static fn (XMLReader $reader): bool => $reader->depth === 1 && in_array($reader->localName, $names, true);
        foreach ($this->scan(1, $accept, $this->readOuterXml(...)) as $fragment) {
            $header .= $fragment;
        }

        return $this->makeValue($header . '</Response>');
    }

    /**
     * Read matching elements in document order. The path starts below the document root.
     * The optional filter can inspect attributes before the element is loaded. Leave the reader on the same node.
     *
     * @param non-empty-list<string> $path
     * @param (callable(XMLReader): bool)|null $filter
     * @return Generator<int, GetValue>
     */
    public function elements(array $path, ?callable $filter = null): Generator
    {
        yield from $this->mapElements($path, $this->readElement(...), $filter);
    }

    /**
     * Read a value from each matching element without importing its subtree into SimpleXML.
     * The callbacks must leave the reader on the same node.
     *
     * @template T
     * @param non-empty-list<string> $path
     * @param callable(XMLReader): T $map
     * @param (callable(XMLReader): bool)|null $filter
     * @return Generator<int, T>
     */
    public function mapElements(array $path, callable $map, ?callable $filter = null): Generator
    {
        $parents = [];
        $depth = count($path);
        $accept = static function (XMLReader $reader) use ($path, $depth, $filter, &$parents): bool {
            if ($reader->depth > $depth || $reader->depth === 0) {
                return false;
            }

            $parents[$reader->depth - 1] = $reader->localName;

            return $reader->depth === $depth && $parents === $path && ($filter === null || $filter($reader));
        };

        yield from $this->scan($depth, $accept, $map);
    }

    /**
     * @template T
     * @param callable(XMLReader): bool $accept
     * @param callable(XMLReader): T $readElement
     * @return Generator<int, T>
     */
    private function scan(int $depth, callable $accept, callable $readElement): Generator
    {
        $stream = $this->response->getBody();

        if (! $stream->isSeekable()) {
            throw new ApiSdkRuntimeException('Reading a streamed XML response requires a seekable body.');
        }

        $position = $stream->tell();
        $stream->rewind();
        $reader = null;

        try {
            $reader = XmlReaderStream::open(ResponseBodyEncoding::decode($this->response)->getBody());
            $skipChildren = false;
            while ($this->read($reader, $skipChildren)) {
                if ($reader->nodeType === XMLReader::DOC_TYPE) {
                    throw new ApiSdkRuntimeException('Document types are unsupported in SDK XML responses.');
                }

                // Let libxml validate descendants that the caller does not need to visit.
                $skipChildren = $reader->nodeType === XMLReader::ELEMENT && $reader->depth >= $depth;

                if ($reader->nodeType === XMLReader::ELEMENT && $accept($reader)) {
                    $value = $readElement($reader);
                    $nextPosition = $stream->tell();

                    // A caller can use the body or another reader while this generator is suspended.
                    $stream->seek($position);
                    yield $value;
                    $stream->seek($nextPosition);
                }
            }
        } finally {
            $reader?->close();
            $stream->seek($position);
        }
    }

    private function read(XMLReader $reader, bool $skipChildren): bool
    {
        $previous = libxml_use_internal_errors(true);
        $existingErrorCount = libxml_get_last_error() === false ? 0 : count(libxml_get_errors());

        try {
            $read = $skipChildren ? $reader->next() : $reader->read();

            if (libxml_get_last_error() !== false) {
                foreach (array_slice(libxml_get_errors(), $existingErrorCount) as $error) {
                    if ($error->level >= LIBXML_ERR_ERROR) {
                        throw new ApiSdkRuntimeException('Invalid SDK XML response: ' . trim($error->message));
                    }
                }
            }

            return $read;
        } finally {
            // Leave diagnostics that belong to the caller in the shared buffer.
            if ($existingErrorCount === 0) {
                libxml_clear_errors();
            }

            libxml_use_internal_errors($previous);
        }
    }

    private function makeValue(string $xml): GetValue
    {
        return new GetValue(new XMLData(new SimpleXMLElement($xml, LIBXML_NONET)));
    }

    private function readOuterXml(XMLReader $reader): string
    {
        $fragment = $reader->readOuterXml();

        if ($fragment === '') {
            throw new ApiSdkRuntimeException('Cannot read an element from the SDK XML response.');
        }

        return $fragment;
    }

    private function readElement(XMLReader $reader): GetValue
    {
        if (! class_exists(DOMDocument::class)) {
            return $this->makeValue($this->readOuterXml($reader));
        }

        // Import one subtree into SimpleXML without writing and parsing its XML again.
        $document = new DOMDocument();
        $node = $reader->expand($document);

        if (! $node instanceof DOMElement) {
            throw new ApiSdkRuntimeException('Cannot read an element from the SDK XML response.');
        }

        $document->appendChild($node);
        $element = simplexml_import_dom($node);

        if (! $element instanceof SimpleXMLElement) {
            throw new ApiSdkRuntimeException('Cannot import an element from the SDK XML response.');
        }

        return new GetValue(new XMLData($element));
    }
}
