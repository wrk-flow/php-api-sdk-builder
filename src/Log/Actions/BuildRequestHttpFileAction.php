<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Actions;

use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Log\Contracts\BuildRequestLogFileActionContract;
use WrkFlow\ApiSdkBuilder\Log\Entities\FileEntity;

/**
 * Generates a file containing the request information in PHPStorm HTTP format.
 *
 * @link https://www.jetbrains.com/help/phpstorm/2023.1/http-client-in-product-code-editor.html
 */
class BuildRequestHttpFileAction implements BuildRequestLogFileActionContract
{
    public function execute(RequestInterface $request, string $basePath): FileEntity
    {
        // TODO? Should we build the file using "stream" ?
        $url = (string) $request->getUri();

        $contents = $request->getMethod() . ' ' . $url . PHP_EOL;

        foreach ($request->getHeaders() as $key => $header) {
            $contents .= $key . ': ' . implode(', ', $header) . PHP_EOL;
        }

        $contents .= PHP_EOL;
        $contents .= $request->getBody();

        return new FileEntity(filePath: $basePath . '.http', contents: $contents);
    }
}
