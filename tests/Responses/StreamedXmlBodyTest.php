<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Responses;

use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use LogicException;
use Nyholm\Psr7\Response as NyholmResponse;
use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Exceptions\ApiSdkRuntimeException;
use WrkFlow\ApiSdkBuilder\Responses\StreamedXmlBody;
use XMLReader;

final class StreamedXmlBodyTest extends TestCase
{
    public function testExistingLibxmlErrorsDoNotAffectAValidResponse(): void
    {
        $previous = libxml_use_internal_errors(true);

        try {
            self::assertFalse(simplexml_load_string('<Unclosed>'));
            $pendingErrors = libxml_get_errors();
            self::assertNotEmpty($pendingErrors);

            $body = new StreamedXmlBody(new Response(200, [], '<Response><Success/></Response>'));
            self::assertNotNull($body->readHeader(['Success'])->getNullableXML('Success'));
            self::assertEquals($pendingErrors, libxml_get_errors());
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    public function testInterleavedReadersAndBodyAccessPreserveAllRecords(): void
    {
        $xml = '<Response><Rates>';
        for ($id = 1; $id <= 100; $id++) {
            $xml .= sprintf('<Rate id="%d">%s</Rate>', $id, str_repeat('x', 256));
        }

        $xml .= '</Rates></Response>';
        $stream = Utils::streamFor($xml);
        $stream->seek(5);

        $body = new StreamedXmlBody(new Response(200, [], $stream));
        $readId = static fn (XMLReader $reader): int => (int) $reader->getAttribute('id');
        $first = $body->mapElements(['Rates', 'Rate'], $readId);
        $second = $body->mapElements(['Rates', 'Rate'], $readId);

        self::assertSame(1, $first->current());
        $secondIds = [];
        for ($id = 1; $id <= 50; $id++) {
            $secondIds[] = $second->current();
            $second->next();
        }

        self::assertSame(substr($xml, 5, 10), $stream->read(10));
        self::assertSame(range(1, 100), iterator_to_array($first));

        while ($second->valid()) {
            $secondIds[] = $second->current();
            $second->next();
        }

        self::assertSame(range(1, 100), $secondIds);
        self::assertSame(5, $stream->tell());
    }

    public function testMapsAttributesAndXmlOnlyForMatchingElements(): void
    {
        $xml = '<Response xmlns:r="urn:rate"><Other><r:Rate id="ignore"/></Other>'
            . '<Rates><r:Rate id="a"/><r:Rate id="b"/></Rates></Response>';
        $body = new StreamedXmlBody(new Response(200, [], $xml));
        $path = ['Rates', 'Rate'];
        $ids = $body->mapElements($path, static fn (XMLReader $reader): ?string => $reader->getAttribute('id'));
        self::assertSame(['a', 'b'], iterator_to_array($ids));

        $fragments = $body->mapElements(
            $path,
            static fn (XMLReader $reader): string => $reader->readOuterXml(),
            static fn (XMLReader $reader): bool => $reader->getAttribute('id') === 'b',
        );
        self::assertSame(['<r:Rate xmlns:r="urn:rate" id="b"/>'], iterator_to_array($fragments));
    }

    public function testFiltersAtTheMatchingElementBeforeReadingItsChildren(): void
    {
        $xml = '<Response><Rates><Rate id="a"><Amount>1</Amount></Rate><Rate id="b"><Amount>2</Amount></Rate>'
            . '</Rates><Other><Rate id="c"/></Other></Response>';
        $body = new StreamedXmlBody(new Response(200, [], $xml));
        $seen = [];

        $items = iterator_to_array($body->elements(['Rates', 'Rate'], static function (XMLReader $reader) use (&$seen): bool {
            $id = $reader->getAttribute('id');
            $seen[] = $id;

            return $id === 'b';
        }));

        self::assertSame(['a', 'b'], $seen);
        self::assertCount(1, $items);
        self::assertSame('2', $items[0]->getRequiredString('Amount'));
    }

    public function testReadsNamespacedElementsAndLeavesTheResponseRewindable(): void
    {
        $xml = '<Response xmlns="urn:sample"><Success/><Rates><Rate id="a"/><Rate id="b"/></Rates>'
            . '<Extra><Rate id="ignore"/></Extra></Response>';
        $stream = FnStream::decorate(Utils::streamFor($xml), [
            '__toString' => static fn () => throw new LogicException('Casting would read the whole body.'),
        ]);
        $stream->seek(5);

        $body = new StreamedXmlBody(new Response(200, [], $stream));

        self::assertNotNull($body->readHeader(['Success', 'Errors'])->getNullableXML('Success'));
        $ids = [];
        foreach ($body->elements(['Rates', 'Rate']) as $element) {
            $ids[] = $element->getXMLAttributesGetter()->getRequiredString('id');
        }

        self::assertSame(['a', 'b'], $ids);
        self::assertSame(5, $stream->tell());
    }

    public function testDecodesCompressedResponsesForBothPasses(): void
    {
        $xml = '<Response><Success/><Rates><Rate id="a"/></Rates></Response>';
        $encoded = gzencode($xml);
        self::assertIsString($encoded);
        $body = new StreamedXmlBody(new Response(200, [
            'Content-Encoding' => 'gzip',
        ], $encoded));

        self::assertNotNull($body->readHeader(['Success'])->getNullableXML('Success'));
        $items = iterator_to_array($body->elements(['Rates', 'Rate']));
        self::assertCount(1, $items);
        self::assertSame('a', $items[0]->getXMLAttributesGetter()->getRequiredString('id'));
    }

    public function testRetainedElementsKeepTheirNamespacedChildrenAfterTheReaderCloses(): void
    {
        $xml = '<Response xmlns="urn:sample" xmlns:details="urn:details"><Rates>'
            . '<Rate id="a"><details:Amount>10</details:Amount></Rate>'
            . '<Rate id="b"><details:Amount>20</details:Amount></Rate></Rates></Response>';
        $body = new StreamedXmlBody(new Response(200, [], $xml));
        $items = iterator_to_array($body->elements(['Rates', 'Rate']));
        unset($body);

        self::assertCount(2, $items);
        self::assertSame('a', $items[0]->getXMLAttributesGetter()->getRequiredString('id'));
        self::assertSame('b', $items[1]->getXMLAttributesGetter()->getRequiredString('id'));
        self::assertSame('10', $items[0]->getRequiredString('details:Amount'));
        self::assertSame('20', $items[1]->getRequiredString('details:Amount'));
    }

    public function testHeaderReadRejectsTruncatedDocuments(): void
    {
        $xml = '<Response><Success/><Rates>' . str_repeat('<Rate/>', 3000) . '</Rates>';
        $body = new StreamedXmlBody(new Response(200, [], $xml));

        $this->expectException(ApiSdkRuntimeException::class);
        $this->expectExceptionMessage('Invalid SDK XML response');
        $body->readHeader(['Success']);
    }

    public function testReadsAnAlreadyDecodedNyholmResponseWithItsOriginalHeaders(): void
    {
        $response = new NyholmResponse(200, [
            'Content-Encoding' => 'gzip',
        ], '<Response><Success/><Rates><Rate id="a"/></Rates></Response>');
        $body = new StreamedXmlBody($response);

        self::assertNotNull($body->readHeader(['Success'])->getNullableXML('Success'));
        $items = iterator_to_array($body->elements(['Rates', 'Rate']));
        self::assertCount(1, $items);
        self::assertSame('a', $items[0]->getXMLAttributesGetter()->getRequiredString('id'));
    }

    public function testRejectsDocumentTypeDeclarations(): void
    {
        $xml = '<!DOCTYPE Response [<!ENTITY example "sample">]><Response>&example;</Response>';
        $body = new StreamedXmlBody(new Response(200, [], $xml));

        $this->expectException(ApiSdkRuntimeException::class);
        $this->expectExceptionMessage('Document types are unsupported');
        $body->readHeader(['Success']);
    }
}
