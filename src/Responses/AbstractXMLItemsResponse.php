<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses;

use Closure;
use Psr\Http\Message\ResponseInterface;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use WrkFlow\ApiSdkBuilder\Transformers\AbstractXMLTransformer;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\Transformers\ArrayItemGetterTransformer;

abstract class AbstractXMLItemsResponse extends AbstractXMLResponse
{
    private readonly AbstractXMLTransformer $transformer;

    public function __construct(
        ResponseInterface $response,
        GetValue $body,
        // It is important that "container" name is used for dependency injection.
        SDKContainerFactoryContract $container
    ) {
        parent::__construct($response, $body);

        $this->transformer = $container->make($this->getTransformerClass());
    }

    abstract protected function getItemsKey(): string;

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
        return $this->body->getArray($this->getItemsKey(), [
            new ArrayItemGetterTransformer(function (GetValue $getValue) {
                return $this->transformer->transform($getValue);
            }),
        ]);
    }

    /**
     * You will receive transformer entity on each item. Returns false if items are empty. Faster than looping items.
     */
    protected function transformUsingLoop(Closure $onItem): bool
    {
        $result = $this->body->getArray($this->getItemsKey(), [
            new ArrayItemGetterTransformer(function (GetValue $getValue) use ($onItem) {
                $result = $this->transformer->transform($getValue);
                $onItem($result);
                return true;
            }),
        ]);

        return $result !== [];
    }

    /**
     * Class must use AbstractJsonTransformer base class.
     *
     * @return class-string<AbstractXMLTransformer>
     */
    abstract protected function getTransformerClass(): string;
}
