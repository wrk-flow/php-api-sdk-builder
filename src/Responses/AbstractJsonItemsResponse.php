<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Closure;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Transformers\AbstractJsonTransformer;
use Wrkflow\GetValue\GetValue;

abstract class AbstractJsonItemsResponse extends AbstractJsonResponse
{
    private readonly AbstractJsonTransformer $transformer;

    public function __construct(
        ResponseInterface $response,
        GetValue $body,
        // It is important that "container" name is used for dependency injection.
        SDKContainerFactoryContract $container
    ) {
        parent::__construct($response, $body);

        $checkKeys = $this->requiredRootKeys();

        if ($checkKeys !== []) {
            $this->checkKeys($body, $checkKeys);
        }

        $this->transformer = $container->make($this->getTransformerClass());
    }

    public function getRawItems(): array
    {
        $itemsKey = $this->itemsKey();
        $root = $this->json();

        if ($itemsKey === null) {
            return $root->data->get();
        }

        return $root->getRequiredArray($itemsKey);
    }

    abstract public function items(): array;

    /**
     * You will receive SPECIFY TYPE on each item. Returns false if items are empty. Faster than looping items.
     *
     * @param Closure(mixed):void $onItem
     */
    abstract public function loopItems(Closure $onItem): bool;

    /**
     * You will receive transformer entity in the array.
     */
    protected function transformUsingArray(): array
    {
        return array_map(fn (array $item) => $this->transformer->transform($item), $this->getRawItems());
    }

    /**
     * You will receive transformer entity on each item. Returns false if items are empty. Faster than looping items.
     */
    protected function transformUsingLoop(Closure $onItem): bool
    {
        $items = $this->getRawItems();

        if ($items === []) {
            return false;
        }

        foreach ($items as $item) {
            $onItem($this->transformer->transform($item));
        }

        return true;
    }

    /**
     * Class must use AbstractJsonTransformer base class.
     *
     * @return class-string<AbstractJsonTransformer>
     */
    abstract protected function getTransformerClass(): string;

    /**
     * Return keys that are required for parsing the response. You can return and empty data.
     *
     * @return array<string>
     */
    abstract protected function requiredRootKeys(): array;

    /**
     * Return key that holds the items (something like data, items) in the root json data. If you return null root json
     * will be used for items.
     */
    abstract protected function itemsKey(): ?string;
}
