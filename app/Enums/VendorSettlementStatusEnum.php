<?php

namespace App\Enums;

enum VendorSettlementStatusEnum: string
{
    use LabelTrait, ValuesTrait;

    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';

    protected function labelKey(): string
    {
        return 'vendor_settlement_status';
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::FAILED, self::CANCELLED => 'danger',
        };
    }
}
