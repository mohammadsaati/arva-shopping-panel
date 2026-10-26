<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    use LabelTrait, ValuesTrait;

    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case ON_HOLD = 'ON_HOLD';
    case SHIPPED = 'SHIPPED';
    case DELIVERED = 'DELIVERED';
    case CANCELLED = 'CANCELLED';

    protected function labelKey(): string
    {
        return 'order_status';
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING, self::ON_HOLD => 'warning',
            self::PROCESSING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function requiresPaidPayment(): bool
    {
        return match ($this) {
            self::PROCESSING, self::SHIPPED, self::DELIVERED => true,
            default => false,
        };
    }
}
