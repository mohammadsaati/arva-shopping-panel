<?php

namespace App\DTO\ProductReview;

use App\DTO\BaseFilterData;

final readonly class ProductReviewPublicFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public int $itemId,
        int $perPage = 5,
        int $page = 1,
        array $sorts = ['approved_at' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
