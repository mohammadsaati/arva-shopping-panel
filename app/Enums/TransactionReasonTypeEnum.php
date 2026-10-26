<?php

namespace App\Enums;

enum TransactionReasonTypeEnum: string
{
    use LabelTrait, ValuesTrait;

    case ORDER = 'ORDER';
    case WALLET = 'WALLET';

    protected function labelKey(): string
    {
        return 'transaction_reason_type';
    }

    public function requiresReasonId(): bool
    {
        return match ($this) {
            self::ORDER, self::WALLET => true,
        };
    }
}
