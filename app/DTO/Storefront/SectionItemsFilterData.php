<?php

namespace App\DTO\Storefront;

use App\DTO\BaseFilterData;

final readonly class SectionItemsFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public int $sectionId,
        public ?string $search = null,
        int $perPage = 12,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
