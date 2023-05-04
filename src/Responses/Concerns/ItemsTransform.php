<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Responses\Concerns;

use Closure;
use WrkFlow\ApiSdkBuilder\Contracts\SDKContainerFactoryContract;
use Wrkflow\GetValue\Contracts\GetValueTransformerContract;
use Wrkflow\GetValue\Contracts\TransformerContract;
use Wrkflow\GetValue\GetValue;
use Wrkflow\GetValue\Transformers\ArrayItemGetterTransformer;

trait ItemsTransform
{
    private GetValueTransformerContract|null $transformer = null;

    abstract public function body(): GetValue;

    abstract public function items(): array;

    /**
     * You will receive SPECIFY TYPE on each item. Returns false if items are empty. Faster than looping items.
     *
     * @param Closure(mixed):void $onItem
     */
    abstract public function loopItems(Closure $onItem): bool;

    /**
     * @param array<TransformerContract>|null $transformers
     */
    public function getRawItems(?array $transformers = null): array
    {
        return $this->body()
            ->getArray($this->getItemsKey(), $transformers);
    }

    abstract protected function container(): SDKContainerFactoryContract;

    /**
     * Class must use AbstractJsonTransformer base class.
     *
     * @return class-string<GetValueTransformerContract>
     */
    abstract protected function getTransformerClass(): string;

    abstract protected function getItemsKey(): string|array;

    /**
     * You will receive transformer entity in the array.
     */
    protected function transformUsingArray(): array
    {
        return $this->getRawItems([new ArrayItemGetterTransformer($this->transformer())]);
    }

    /**
     * You will receive transformer entity on each item. Returns false if items are empty. Faster than looping items.
     */
    protected function transformUsingLoop(Closure $onItem): bool
    {
        $result = $this->getRawItems([
            new ArrayItemGetterTransformer(function (GetValue $getValue, string $key) use ($onItem): bool {
                $result = $this->transformer()
                    ->transform($getValue, $key);
                $onItem($result);
                return true;
            }),
        ]);

        return $result !== [];
    }

    private function transformer(): GetValueTransformerContract
    {
        if ($this->transformer === null) {
            $this->transformer = $this->container()
                ->make($this->getTransformerClass());
        }

        return $this->transformer;
    }
}
