<?php

declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilder\Response\Concerns;

/**
 * Paginated resource information - if not set it will throw PHP error.
 */
trait PaginatedResource
{
    protected int $itemsPerPage;

    protected int $totalItems;

    protected int $currentPage;

    protected int $totalPages;

    public function getItemsPerPage(): int
    {
        return $this->itemsPerPage;
    }

    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    public function onLastPage(): bool
    {
        return $this->currentPage === $this->totalPages;
    }
}
