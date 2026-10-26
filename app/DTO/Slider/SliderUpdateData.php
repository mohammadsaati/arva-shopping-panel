<?php

namespace App\DTO\Slider;

use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
use Illuminate\Http\UploadedFile;

final readonly class SliderUpdateData
{
    public function __construct(
        public ?string $title = null,
        public ?int $priority = null,
        public ?UploadedFile $image = null,
        public ?SliderTypeEnum $type = null,
        public ?int $productId = null,
        public bool $productIdProvided = false,
        public ?int $categoryId = null,
        public bool $categoryIdProvided = false,
        public ?int $sectionId = null,
        public bool $sectionIdProvided = false,
        public ?string $link = null,
        public bool $linkProvided = false,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, string|int|SliderTypeEnum|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'title' => $this->title,
            'priority' => $this->priority,
            'type' => $this->type,
            'status' => $this->status,
        ], static fn (string|int|SliderTypeEnum|GlobalStatusEnum|null $value): bool => $value !== null);

        foreach ([
            'product_id' => [$this->productIdProvided, $this->productId],
            'category_id' => [$this->categoryIdProvided, $this->categoryId],
            'section_id' => [$this->sectionIdProvided, $this->sectionId],
            'link' => [$this->linkProvided, $this->link],
        ] as $attribute => [$provided, $value]) {
            if ($provided) {
                $attributes[$attribute] = $value;
            }
        }

        return $attributes;
    }
}
