<?php

namespace App\DTO\Blog;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BlogCategoryCreateData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?int $parentId,
        public ?string $description,
        public ?UploadedFile $image,
        public int $priority,
        public GlobalStatusEnum $status,
    ) {
    }
}
