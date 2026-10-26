<?php

namespace App\DTO\Order;

use App\Enums\OrderStatusEnum;

final readonly class OrderStatusUpdateData
{
    public function __construct(public ?OrderStatusEnum $status = null)
    {
    }
}
