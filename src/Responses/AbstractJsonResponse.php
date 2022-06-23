<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\BodyIsJsonContract;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;

abstract class AbstractJsonResponse extends AbstractResponse implements BodyIsJsonContract
{
    public function __construct(
        ResponseInterface $response,
        protected array $json
    ) {
        parent::__construct($response);
    }

    /**
     * Response must be successful to access json.
     */
    public function json(): array
    {
        return $this->json;
    }

    protected function checkKeys(array $json, array $keys): void
    {
        if ($keys === []) {
            return;
        }

        $missingKeys = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $json) === false) {
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys === []) {
            return;
        }

        $message = 'Response is missing required keys: ' . implode(',', $missingKeys);
        throw new InvalidJsonResponseException($this->response, $message, $json);
    }
}
