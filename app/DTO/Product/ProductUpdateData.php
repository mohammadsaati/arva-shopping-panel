<?php

namespace App\DTO\Product;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class ProductUpdateData
{
    /**
     * @param array<int, UploadedFile> $images
     * @param array<int, UploadedFile> $replacements Image ID to uploaded file.
     * @param array<int, int> $deleteImageIds
     * @param array<int, ProductCityRateData> $cityRates
     */
    public function __construct(
        public ?int $itemId = null,
        public ?int $price = null,
        public ?int $colorId = null,
        public bool $colorIdProvided = false,
        public ?int $sizeId = null,
        public bool $sizeIdProvided = false,
        public ?int $stock = null,
        public ?int $shippingPrice = null,
        public ?GlobalStatusEnum $status = null,
        public array $images = [],
        public array $replacements = [],
        public array $deleteImageIds = [],
        public array $cityRates = [],
        public bool $cityRatesProvided = false,
    ) {
    }

    /** @return array<string, int|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'item_id' => $this->itemId,
            'price' => $this->price,
            'stock' => $this->stock,
            'shipping_price' => $this->shippingPrice,
            'status' => $this->status,
        ], static fn (int|GlobalStatusEnum|null $value): bool => $value !== null);

        if ($this->colorIdProvided) {
            $attributes['color_id'] = $this->colorId;
        }

        if ($this->sizeIdProvided) {
            $attributes['size_id'] = $this->sizeId;
        }

        return $attributes;
    }
}
