<?php

namespace App\Enums;

enum BlogPostStatusEnum: string
{
    use LabelTrait, ValuesTrait;

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';

    protected function labelKey(): string
    {
        return 'blog_post_status';
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'warning',
            self::PUBLISHED => 'success',
            self::ARCHIVED => 'gray',
        };
    }
}
