<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Actions;

class GetExtensionFromContentTypeAction
{
    public function execute(string $contentType): string
    {
        $clean = trim(explode(separator: ';', string: $contentType)[0]);
        return match (true) {
            str_ends_with($clean, 'json') => 'json',
            str_ends_with($clean, 'xhtml+xml'),
            str_ends_with($clean, 'html') => 'html',
            str_ends_with($clean, 'svg+xml') => 'svg',
            str_ends_with($clean, 'xml') => 'xml',
            default => 'txt',
        };
    }
}
