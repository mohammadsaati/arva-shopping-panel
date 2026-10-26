<?php

namespace App\DTO\Category;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class CategoryCreateData
{
    public function __construct(
        public string $name,
        public ?int $parentId,
        public string $slug,
        public int $priority,
        public ?string $icon,
        public ?UploadedFile $image,
        public GlobalStatusEnum $status,
    ) {
    }
}
