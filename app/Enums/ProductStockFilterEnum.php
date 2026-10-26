<?php

namespace App\Enums;

enum ProductStockFilterEnum: string
{
    use LabelTrait, ValuesTrait;

    case IN_STOCK = 'IN_STOCK';
    case LOW_STOCK = 'LOW_STOCK';
    case OUT_OF_STOCK = 'OUT_OF_STOCK';

    public const DEFAULT_LOW_THRESHOLD = 5;

    protected function labelKey(): string
    {
        return 'product_stock_filter';
    }

    public function color(): string
    {
        return match ($this) {
            self::IN_STOCK => 'success',
            self::LOW_STOCK => 'warning',
            self::OUT_OF_STOCK => 'danger',
        };
    }

    public function defaultLowThreshold(): int
    {
        return self::DEFAULT_LOW_THRESHOLD;
    }

    public function includesOnlyZero(): bool
    {
        return $this === self::OUT_OF_STOCK;
    }

    public function isLowStockBand(): bool
    {
        return $this === self::LOW_STOCK;
    }
}
