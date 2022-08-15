<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Contracts\BodyIsJsonContract;
use WrkFlow\ApiSdkBuilder\Contracts\BodyIsXmlContract;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;
use Wrkflow\GetValue\DataHolders\ArrayData;
use Wrkflow\GetValue\DataHolders\XMLData;
use Wrkflow\GetValue\GetValue;

class MakeBodyFromResponseAction
{
    public function execute(string $responseClass, ResponseInterface $response): ?GetValue
    {
        $implements = class_implements($responseClass);

        if (is_array($implements) === false) {
            return null;
        }

        if (array_key_exists(BodyIsJsonContract::class, $implements)) {
            return $this->convertToJson($response);
        }

        if (array_key_exists(BodyIsXmlContract::class, $implements)) {
            return new GetValue(new XMLData(new SimpleXMLElement($response->getBody()->getContents())));
        }

        return null;
    }

    protected function convertToJson(ResponseInterface $response): GetValue
    {
        $json = null;
        try {
            $content = (string) $response->getBody();
            $json = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

            if (is_array($json) === false) {
                throw new InvalidJsonResponseException($response, 'Response is not a json');
            }

            return new GetValue(new ArrayData($json));
        } catch (JsonException $jsonException) {
            throw new InvalidJsonResponseException($response, 'Failed to build json: ' . $jsonException->getMessage(), $json, $jsonException);
        }
    }
}
