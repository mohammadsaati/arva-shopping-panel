<?php

namespace App\DTO\Blog;

use App\DTO\BaseFilterData;
use App\Enums\BlogPostStatusEnum;

final readonly class BlogPostFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        public ?int $blogCategoryId = null,
        public ?BlogPostStatusEnum $status = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
