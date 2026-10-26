<?php

namespace App\DTO\Storefront;

use App\DTO\BaseFilterData;

final readonly class CatalogFilterData extends BaseFilterData
{
    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?int $brandId = null,
        public ?int $minPrice = null,
        public ?int $maxPrice = null,
        public bool $inStockOnly = true,
        int $perPage = 12,
        int $page = 1,
        array $sorts = ['created_at' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
