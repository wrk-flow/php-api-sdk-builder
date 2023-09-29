<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Log\Actions;

use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class GetTextForLogAction
{
    /**
     * GET [OK] 200 example.com /api/v1/clients/1 [20s]
     * GET [OK] 300 example.com  /api/v1/clients/1 [20s]
     * GET [FAILED] 500 example.com /api/v1/clients/1 [20s]
     * GET [FAILED] 000 example.com /api/v1/clients/1 [20s].
     */
    public function execute(
        RequestInterface $request,
        float $requestDurationInSeconds,
        ?ResponseInterface $response = null,
        ?Exception $exception = null
    ): string {
        $uri = $request->getUri();

        $statusCode = $response instanceof ResponseInterface === false
            ? '000'
            : (string) $response->getStatusCode();

        return sprintf(
            '%s [%s] %s %s %s [%ds]',
            $request->getMethod(),
            $exception instanceof Exception ? 'FAILED' : 'OK',
            $statusCode,
            $uri->getHost(),
            $uri->getPath(),
            $requestDurationInSeconds
        );
    }
}
