<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Entities;

class FileEntity
{
    public function __construct(
        public readonly string $filePath,
        public readonly string $contents,
    ) {
    }
}
