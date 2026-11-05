<?php declare(strict_types=1);

namespace WrkFlow\ApiSdkBuilderTests\Responses;

use PHPUnit\Framework\TestCase;
use WrkFlow\ApiSdkBuilder\Responses\Concerns\PaginatedResponse;

final class PaginatedResponseTest extends TestCase
{
    public function testPaginationMetadataAndLastPage(): void
    {
        foreach ([[0, 1, 0, true], [25, 1, 3, false], [25, 3, 3, true]] as [$total, $page, $pages, $last]) {
            $response = new class($total, $page, $pages) {
                use PaginatedResponse;

                public function __construct(int $total, int $page, int $pages)
                {
                    $this->itemsPerPage = 10;
                    $this->totalItems = $total;
                    $this->currentPage = $page;
                    $this->totalPages = $pages;
                }
            };

            self::assertSame(10, $response->getItemsPerPage());
            self::assertSame($total, $response->getTotalItems());
            self::assertSame($page, $response->getCurrentPage());
            self::assertSame($pages, $response->getTotalPages());
            self::assertSame($last, $response->onLastPage());
        }
    }
}
