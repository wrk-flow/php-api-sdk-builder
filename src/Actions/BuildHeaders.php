<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Psr\Http\Message\RequestInterface;
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class BuildHeaders
{
    /**
     * @param array<int|string,HeadersContract|string|string[]> $headers
     */
    public function execute(array $headers, RequestInterface $request): RequestInterface
    {
        foreach ($headers as $name => $header) {
            if ($header instanceof HeadersContract) {
                $request = $this->execute($header->headers(), $request);
            } else {
                $request = $request->withAddedHeader((string) $name, $header);
            }
        }

        return $request;
    }
}
