<?php

namespace App\DTO\Blog;

use App\DTO\BaseFilterData;

final readonly class BlogCategoryPostsFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public int $categoryId,
        public ?string $search = null,
        int $perPage = 9,
        int $page = 1,
        array $sorts = ['published_at' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
