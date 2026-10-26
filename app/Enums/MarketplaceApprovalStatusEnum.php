<?php

namespace App\Enums;

enum MarketplaceApprovalStatusEnum: string
{
    use LabelTrait, ValuesTrait;

    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    protected function labelKey(): string
    {
        return 'marketplace_approval_status';
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
