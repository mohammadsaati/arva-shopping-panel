<?php

namespace App\DTO\Section;

use App\DTO\BaseFilterData;

final readonly class SectionItemFilterData extends BaseFilterData
{
    /**
     * @param array<int, int> $selectedItemIds
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        public ?string $search = null,
        public array $selectedItemIds = [],
        public bool $selectedOnly = false,
        public ?bool $homeAvailable = null,
        int $perPage = 10,
        int $page = 1,
        array $sorts = ['name' => 'asc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
