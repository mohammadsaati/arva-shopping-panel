<?php

namespace App\DTO\Banner;

use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BannerUpdateData
{
    public function __construct(
        public ?UploadedFile $image = null,
        public ?BannerTypeEnum $type = null,
        public ?int $productId = null,
        public bool $productIdProvided = false,
        public ?int $categoryId = null,
        public bool $categoryIdProvided = false,
        public ?string $link = null,
        public bool $linkProvided = false,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, string|int|BannerTypeEnum|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'type' => $this->type,
            'status' => $this->status,
        ], static fn (string|int|BannerTypeEnum|GlobalStatusEnum|null $value): bool => $value !== null);

        if ($this->productIdProvided) {
            $attributes['product_id'] = $this->productId;
        }

        if ($this->categoryIdProvided) {
            $attributes['category_id'] = $this->categoryId;
        }

        if ($this->linkProvided) {
            $attributes['link'] = $this->link;
        }

        return $attributes;
    }
}
