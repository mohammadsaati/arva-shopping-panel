<?php

namespace App\DTO\Blog;

use App\Enums\BlogPostStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BlogPostUpdateData
{
    public function __construct(
        public ?int $blogCategoryId = null,
        public ?string $title = null,
        public ?string $slug = null,
        public ?string $excerpt = null,
        public bool $excerptProvided = false,
        public ?string $body = null,
        public bool $bodyProvided = false,
        public ?UploadedFile $featuredImage = null,
        public bool $removeFeaturedImage = false,
        public ?BlogPostStatusEnum $status = null,
        public ?bool $isFeatured = null,
        public ?int $featuredOrder = null,
        public ?string $metaTitle = null,
        public bool $metaTitleProvided = false,
        public ?string $metaDescription = null,
        public bool $metaDescriptionProvided = false,
        public ?string $publishedAt = null,
        public bool $publishedAtProvided = false,
    ) {
    }

    /** @return array<string, string|int|bool|BlogPostStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'blog_category_id' => $this->blogCategoryId,
            'title' => $this->title,
            'slug' => $this->slug,
            'status' => $this->status,
            'is_featured' => $this->isFeatured,
            'featured_order' => $this->featuredOrder,
        ], static fn (string|int|bool|BlogPostStatusEnum|null $value): bool => $value !== null);

        foreach ([
            'excerpt' => [$this->excerptProvided, $this->excerpt],
            'body' => [$this->bodyProvided, $this->body],
            'meta_title' => [$this->metaTitleProvided, $this->metaTitle],
            'meta_description' => [$this->metaDescriptionProvided, $this->metaDescription],
            'published_at' => [$this->publishedAtProvided, $this->publishedAt],
        ] as $attribute => [$provided, $value]) {
            if ($provided) {
                $attributes[$attribute] = $value;
            }
        }

        return $attributes;
    }
}
