<?php

namespace App\DTO\Specification;

use App\DTO\BaseFilterData;

final readonly class SpecificationCategoryFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['name' => 'asc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
