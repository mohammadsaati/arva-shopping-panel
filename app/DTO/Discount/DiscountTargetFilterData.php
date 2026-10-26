<?php

namespace App\DTO\Discount;

use App\DTO\BaseFilterData;

final readonly class DiscountTargetFilterData extends BaseFilterData
{
    /** @param array<int, int> $selectedIds @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public string $targetType,
        public ?string $search = null,
        public array $selectedIds = [],
        public bool $selectedOnly = false,
        int $perPage = 10,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
