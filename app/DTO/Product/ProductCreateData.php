<?php

namespace App\DTO\Product;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class ProductCreateData
{
    /**
     * @param array<int, UploadedFile> $images
     * @param array<int, ProductCityRateData> $cityRates
     */
    public function __construct(
        public int $itemId,
        public int $price,
        public ?int $colorId,
        public ?int $sizeId,
        public int $stock,
        public int $shippingPrice,
        public GlobalStatusEnum $status,
        public array $images = [],
        public array $cityRates = [],
    ) {
    }
}
