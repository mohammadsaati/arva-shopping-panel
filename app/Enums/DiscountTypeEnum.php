<?php

namespace App\Enums;

enum DiscountTypeEnum: string
{
    use LabelTrait, ValuesTrait;

    case FIXED = 'FIXED';
    case PERCENTAGE = 'PERCENTAGE';

    protected function labelKey(): string
    {
        return 'discount_type';
    }

    public function color(): string
    {
        return match ($this) {
            self::FIXED => 'warning',
            self::PERCENTAGE => 'info',
        };
    }
}
