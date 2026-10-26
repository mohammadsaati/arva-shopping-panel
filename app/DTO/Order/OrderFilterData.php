<?php

namespace App\DTO\Order;

use App\DTO\BaseFilterData;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;

final readonly class OrderFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        public ?OrderStatusEnum $status = null,
        public ?PaymentStatusEnum $paymentStatus = null,
        public ?int $userId = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
