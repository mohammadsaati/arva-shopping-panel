<?php

namespace App\DTO\Blog;

use App\Enums\BlogPostStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BlogPostCreateData
{
    public function __construct(
        public int $blogCategoryId,
        public ?int $authorId,
        public string $title,
        public string $slug,
        public ?string $excerpt,
        public ?string $body,
        public ?UploadedFile $featuredImage,
        public BlogPostStatusEnum $status,
        public bool $isFeatured,
        public int $featuredOrder,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public ?string $publishedAt,
    ) {
    }
}
