<?php

namespace App\DTO\Payment;

use App\DTO\BaseFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentGatewayDriverEnum;

final readonly class PaymentGatewayFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        public ?GlobalStatusEnum $status = null,
        public ?PaymentGatewayDriverEnum $driver = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'asc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
