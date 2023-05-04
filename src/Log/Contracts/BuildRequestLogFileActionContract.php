<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Contracts;

use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Log\Entities\FileEntity;

/**
 * Generates a file containing the request information in PHPStorm HTTP format.
 */
interface BuildRequestLogFileActionContract
{
    public function execute(RequestInterface $request, string $basePath): FileEntity;
}
