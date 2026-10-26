<?php

namespace App\DTO\Item;

use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

final readonly class ItemUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?int $code = null,
        public ?int $categoryId = null,
        public ?int $brandId = null,
        public bool $brandIdProvided = false,
        public ?UploadedFile $image = null,
        public ?string $description = null,
        public bool $descriptionProvided = false,
        public ?GlobalStatusEnum $status = null,
        public ?bool $isAmazing = null,
        public ?Carbon $amazingEndTime = null,
        public bool $amazingEndTimeProvided = false,
        /** @var array<int, \App\DTO\Specification\SpecificationAnswerData> */
        public array $specifications = [],
        public bool $specificationsProvided = false,
    ) {
    }

    /** @return array<string, mixed> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'category_id' => $this->categoryId,
            'status' => $this->status,
            'is_amazing' => $this->isAmazing,
        ], static fn (string|int|bool|GlobalStatusEnum|null $value): bool => $value !== null);

        if ($this->brandIdProvided) {
            $attributes['brand_id'] = $this->brandId;
        }

        if ($this->descriptionProvided) {
            $attributes['description'] = $this->description;
        }

        if ($this->amazingEndTimeProvided) {
            $attributes['amazing_end_time'] = $this->amazingEndTime;
        }

        return $attributes;
    }
}
