<?php

namespace App\Enums;

enum PaymentStatusEnum: string
{
    use LabelTrait, ValuesTrait;

    case PENDING = 'PENDING';
    case PAID = 'PAID';
    case FAILED = 'FAILED';

    protected function labelKey(): string
    {
        return 'payment_status';
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::FAILED => 'danger',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function canRetry(): bool
    {
        return $this === self::PENDING || $this === self::FAILED;
    }
}
