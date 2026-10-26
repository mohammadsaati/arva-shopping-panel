<?php

namespace App\DTO\Category;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class CategoryUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?int $parentId = null,
        public bool $parentIdProvided = false,
        public ?string $slug = null,
        public ?int $priority = null,
        public ?string $icon = null,
        public bool $iconProvided = false,
        public ?UploadedFile $image = null,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /**
     * Return only supplied fields so omitted values do not overwrite stored data.
     * Nullable parent and icon values are included when their inputs were submitted.
     *
     * @return array<string, string|int|GlobalStatusEnum|null>
     */
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

        if ($this->iconProvided) {
            $attributes['icon'] = $this->icon;
        }

        return $attributes;
    }
}
