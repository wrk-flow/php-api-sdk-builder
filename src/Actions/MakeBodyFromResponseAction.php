<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Actions;

use GuzzleHttp\Psr7\InflateStream;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Exceptions\InvalidJsonResponseException;
use WrkFlow\ApiSdkBuilder\Interfaces\BodyIsJsonInterface;
use WrkFlow\ApiSdkBuilder\Interfaces\BodyIsXmlInterface;
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

        $encoding = strtolower($response->getHeaderLine('Content-Encoding'));
        if ($encoding === 'gzip' || $encoding === 'deflate') {
            $response = $response
                ->withBody(new InflateStream($response->getBody()))
                ->withoutHeader('Content-Encoding');
        }

        if (array_key_exists(BodyIsJsonInterface::class, $implements)) {
            return $this->convertToJson($response);
        }

        if (array_key_exists(BodyIsXmlInterface::class, $implements)) {
            return new GetValue(new XMLData(new SimpleXMLElement((string) $response->getBody())));
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
            throw new InvalidJsonResponseException(
                $response,
                'Failed to build json: ' . $jsonException->getMessage(),
                $json,
                $jsonException
            );
        }
    }
}
