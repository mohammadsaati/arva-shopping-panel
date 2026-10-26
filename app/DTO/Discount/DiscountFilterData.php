<?php

namespace App\DTO\Discount;

use App\DTO\BaseFilterData;
use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;

final readonly class DiscountFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        public ?GlobalStatusEnum $status = null,
        public ?DiscountTypeEnum $type = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
