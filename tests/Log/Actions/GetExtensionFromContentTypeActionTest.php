<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Log\Actions;

use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Log\Actions\GetExtensionFromContentTypeAction;

final class GetExtensionFromContentTypeActionTest extends TestCase
{
    private GetExtensionFromContentTypeAction $getExtensionFromContentTypeAction;

    protected function setUp(): void
    {
        $this->getExtensionFromContentTypeAction = new GetExtensionFromContentTypeAction();
    }

    /**
     * @dataProvider contentTypeProvider
     */
    public function testExecute(string $contentType, mixed $expectedExtension): void
    {
        $this->assertEquals(
            expected: $expectedExtension,
            actual: $this->getExtensionFromContentTypeAction->execute($contentType),
            message: sprintf('Content type %s should return %s extension', $contentType, $expectedExtension),
        );
    }

    public function contentTypeProvider(): array
    {
        return [
            ['application/json', 'json'],
            ['text/json', 'json'],
            ['text/xml', 'xml'],
            ['application/xml', 'xml'],
            ['application/rss+xml', 'xml'],
            ['text/plain', 'txt'],
            ['application/pdf', 'txt'],
            ['text/html', 'html'],
            ['application/json; charset=utf-8', 'json'],
            ['text/json; charset=utf-8', 'json'],
            ['text/xml; charset=utf-8', 'xml'],
            ['application/xml; charset=utf-8', 'xml'],
            ['application/rss+xml; charset=utf-8', 'xml'],
            ['text/plain; charset=utf-8', 'txt'],
            ['application/pdf; charset=utf-8', 'txt'],
            ['text/html; charset=utf-8', 'html'],
            ['multipart/form-data; boundary=something', 'txt'],
            ['application/EDI-X12', 'txt'],
            ['application/EDIFACT', 'txt'],
            ['application/javascript', 'txt'],
            ['application/octet-stream', 'txt'],
            ['application/ogg', 'txt'],
            ['application/pdf', 'txt'],
            ['application/xhtml+xml', 'html'],
            ['application/x-shockwave-flash', 'txt'],
            ['application/ld+json', 'json'],
            ['application/zip', 'txt'],
            ['application/x-www-form-urlencoded', 'txt'],
            ['image/gif', 'txt'],
            ['image/jpeg', 'txt'],
            ['image/png', 'txt'],
            ['image/tiff', 'txt'],
            ['image/vnd.microsoft.icon', 'txt'],
            ['image/x-icon', 'txt'],
            ['image/vnd.djvu', 'txt'],
            ['image/svg+xml', 'svg'],
            ['text/css', 'txt'],
            ['text/csv', 'txt'],
            ['video/mpeg', 'txt'],
            ['video/mp4', 'txt'],
            ['video/quicktime', 'txt'],
            ['video/x-ms-wmv', 'txt'],
            ['video/x-msvideo', 'txt'],
            ['video/x-flv', 'txt'],
            ['video/webm', 'txt'],
            ['application/vnd.oasis.opendocument.text', 'txt'],
            ['application/vnd.oasis.opendocument.spreadsheet', 'txt'],
            ['application/vnd.oasis.opendocument.presentation', 'txt'],
            ['application/vnd.oasis.opendocument.graphics', 'txt'],
            ['application/vnd.ms-excel', 'txt'],
            ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'txt'],
            ['application/vnd.ms-powerpoint', 'txt'],
            ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'txt'],
            ['application/msword', 'txt'],
            ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'txt'],
            ['application/vnd.mozilla.xul+xml', 'xml'],
        ];
    }
}
