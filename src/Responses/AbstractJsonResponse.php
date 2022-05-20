<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use TypeError;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;

abstract class AbstractJsonResponse extends AbstractResponse
{
    protected array $json;

    public function __construct(ResponseInterface $response)
    {
        $json = null;
        parent::__construct($response);

        try {
            $content = (string) $response->getBody();
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (is_array($json) === false) {
                throw new InvalidJsonResponseException($response, 'Response is not a json');
            }

            $this->parseJson($json);
            $this->json = $json;
        } catch (JsonException $jsonException) {
            throw new InvalidJsonResponseException(
                $response,
                'Failed to build json: ' . $jsonException->getMessage(),
                $json,
                $jsonException
            );
        } catch (TypeError $typeError) {
            throw new InvalidJsonResponseException(
                $response,
                'Failed to parse json: ' . $typeError->getMessage(),
                $json,
                $typeError
            );
        }
    }

    /**
     * Response must be successful to access json.
     */
    public function json(): array
    {
        return $this->json;
    }

    abstract protected function parseJson(array $json): void;

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
