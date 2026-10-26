<?php

namespace App\DTO\Item;

use App\DTO\Product\ProductCityRateData;
use App\Enums\GlobalStatusEnum;

final readonly class ItemProductCreateData
{
    /** @param array<int, ProductCityRateData> $cityRates */
    public function __construct(
        public int $price,
        public ?int $colorId,
        public ?int $sizeId,
        public int $stock,
        public int $shippingPrice,
        public GlobalStatusEnum $status,
        public array $cityRates = [],
    ) {
    }
}
