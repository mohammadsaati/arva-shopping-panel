<?php

namespace App\DTO\VendorPanel;

final readonly class VendorProductCreateData
{
    public function __construct(
        public int $itemId,
        public int $price,
        public int $stock,
        public int $shippingPrice,
        public ?int $colorId,
        public ?int $sizeId,
        /** @var array<int, \Illuminate\Http\UploadedFile> */
        public array $images = [],
    ) {
    }
}
