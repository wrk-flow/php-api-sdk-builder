<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use Psr\Http\Message\MessageInterface;
use WrkFlow\ApiSdkBuilder\Contracts\HeadersContract;

class BuildHeadersAction
{
    /**
     * @template T of MessageInterface
     * @param array<int|string,HeadersContract|string|string[]> $headers
     * @param T $message
     * @return T
     */
    public function execute(array $headers, MessageInterface $message): MessageInterface
    {
        foreach ($headers as $name => $header) {
            if ($header instanceof HeadersContract) {
                $message = $this->execute($header->headers(), $message);
            } else {
                $message = $message->withAddedHeader((string) $name, $header);
            }
        }

        return $message;
    }
}
