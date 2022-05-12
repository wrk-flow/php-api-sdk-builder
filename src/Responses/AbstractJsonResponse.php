<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Psr\Http\Message\ResponseInterface;
use TypeError;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;

abstract class AbstractJsonResponse extends AbstractResponse
{
    private bool $isSuccessful = false;

    private array $json;

    public function __construct(ResponseInterface $response)
    {
        parent::__construct($response);

        $content = (string) $response->getBody();
        $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (is_array($json) === false) {
            throw new InvalidJsonResponseException('Response is no a json');
        }

        try {
            $this->isSuccessful = $this->parseJson($json);

            if ($this->isSuccessful) {
                $this->json = $json;
            }
        } catch (TypeError $typeError) {
            throw new InvalidJsonResponseException(
                'Failed to parse json: ' . $typeError->getMessage(),
                $json,
                $typeError->getCode(),
                $typeError
            );
        }
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * Response must be successful to access json.
     */
    public function toArray(): array
    {
        return $this->json;
    }

    abstract protected function parseJson(array $json): bool;

    protected function hasKeys(array $json, array $keys): bool
    {
        if ($keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            if (array_key_exists($key, $json) === false) {
                return false;
            }
        }

        return true;
    }
}
