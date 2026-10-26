<?php

namespace App\DTO\Product;

use App\DTO\BaseFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\ProductStockFilterEnum;

final readonly class ProductFilterData extends BaseFilterData
{
    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        public ?string $search = null,
        public ?GlobalStatusEnum $status = null,
        public ?int $itemId = null,
        public ?ProductStockFilterEnum $stockFilter = null,
        public ?int $colorId = null,
        public ?int $sizeId = null,
        public int $lowStockThreshold = ProductStockFilterEnum::DEFAULT_LOW_THRESHOLD,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }

    public function resolvedLowStockThreshold(): int
    {
        return max(1, $this->lowStockThreshold);
    }
}
