<?php

namespace App\DTO\Blog;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BlogCategoryUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?int $parentId = null,
        public bool $parentIdProvided = false,
        public ?string $description = null,
        public bool $descriptionProvided = false,
        public ?UploadedFile $image = null,
        public bool $removeImage = false,
        public ?int $priority = null,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, string|int|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'priority' => $this->priority,
            'status' => $this->status,
        ], static fn (string|int|GlobalStatusEnum|null $value): bool => $value !== null);

        if ($this->parentIdProvided) {
            $attributes['parent_id'] = $this->parentId;
        }

        if ($this->descriptionProvided) {
            $attributes['description'] = $this->description;
        }

        return $attributes;
    }
}
