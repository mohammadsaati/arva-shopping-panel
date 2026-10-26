<?php

namespace App\DTO\Order;

use App\DTO\BaseFilterData;
use App\Enums\OrderStatusEnum;

final readonly class OrderHistoryFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?OrderStatusEnum $status = null,
        int $perPage = 10,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
        public ?string $search = null,
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
