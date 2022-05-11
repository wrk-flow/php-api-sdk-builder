<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Response;

use Closure;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Transformers\AbstractXMLTransformer;

abstract class AbstractXMLItemsResponse extends AbstractXMLResponse
{
    private readonly AbstractXMLTransformer $transformer;

    public function __construct(
        ResponseInterface $response,
        // It is important that "container" name is used for dependency injection.
        SDKContainerFactoryContract $container
    ) {
        parent::__construct($response);

        $this->transformer = $container->make($this->getTransformerClass());
    }

    abstract public function getRawItems(): SimpleXMLElement;

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
        $items = $this->isSuccessful() ? $this->getRawItems() : [];

        if ($items === []) {
            return [];
        }

        return array_map(fn (array $item) => $this->transformer->transform($item), $items);
    }

    /**
     * You will receive transformer entity on each item. Returns false if items are empty. Faster than looping items.
     */
    protected function transformUsingLoop(Closure $onItem): bool
    {
        $items = $this->isSuccessful() ? $this->getRawItems() : [];

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
     * @return class-string<AbstractXMLTransformer>
     */
    abstract protected function getTransformerClass(): string;
}
