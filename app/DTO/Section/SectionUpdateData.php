<?php

namespace App\DTO\Section;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class SectionUpdateData
{
    /**
     * @param array<int, UploadedFile> $images
     * @param array<int, int> $deleteImageIds
     * @param array<int, int>|null $itemIds
     */
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $bgColor = null,
        public bool $bgColorProvided = false,
        public ?UploadedFile $bgImage = null,
        public bool $removeBgImage = false,
        public array $images = [],
        public array $deleteImageIds = [],
        public ?GlobalStatusEnum $status = null,
        public ?array $itemIds = null,
        public bool $itemIdsProvided = false,
    ) {
    }

    /** @return array<string, string|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
        ], static fn (string|GlobalStatusEnum|null $value): bool => $value !== null);

        if ($this->bgColorProvided) {
            $attributes['bg_color'] = $this->bgColor;
        }

        return $attributes;
    }
}
