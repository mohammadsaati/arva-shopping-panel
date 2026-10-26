<?php

namespace App\DTO\Brand;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BrandUpdateData
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?UploadedFile $image = null,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, string|GlobalStatusEnum> */
    public function toModelAttributes(): array
    {
        return array_filter([
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => $this->status,
        ], static fn (string|GlobalStatusEnum|null $value): bool => $value !== null);
    }
}
