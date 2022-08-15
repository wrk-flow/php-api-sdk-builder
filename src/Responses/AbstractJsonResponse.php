<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\BodyIsJsonContract;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;
use Wrkflow\GetValue\GetValue;

abstract class AbstractJsonResponse extends AbstractResponse implements BodyIsJsonContract
{
    public function __construct(
        ResponseInterface $response,
        protected readonly GetValue $body
    ) {
        parent::__construct($response);
    }

    /**
     * Response must be successful to access json.
     */
    public function json(): GetValue
    {
        return $this->body;
    }

    protected function checkKeys(GetValue $json, array $keys): void
    {
        if ($keys === []) {
            return;
        }

        $missingKeys = [];
        foreach ($keys as $key) {
            if ($json->data->getValue($key) !== null) {
                $missingKeys[] = $key;
            }
        }

        if ($missingKeys === []) {
            return;
        }

        $message = 'Response is missing required keys: ' . implode(',', $missingKeys);
        throw new InvalidJsonResponseException($this->response, $message, $json->data->get());
    }
}
