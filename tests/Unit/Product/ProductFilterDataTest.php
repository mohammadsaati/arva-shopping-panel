<?php

namespace Tests\Unit\Product;

use App\DTO\Product\ProductFilterData;
use App\Enums\ProductStockFilterEnum;
use PHPUnit\Framework\TestCase;

class ProductFilterDataTest extends TestCase
{
    public function test_resolved_low_stock_threshold_defaults_to_enum_constant(): void
    {
        $filters = new ProductFilterData(stockFilter: ProductStockFilterEnum::LOW_STOCK);

        $this->assertSame(ProductStockFilterEnum::DEFAULT_LOW_THRESHOLD, $filters->resolvedLowStockThreshold());
    }

    public function test_resolved_low_stock_threshold_never_goes_below_one(): void
    {
        $filters = new ProductFilterData(lowStockThreshold: 0);

        $this->assertSame(1, $filters->resolvedLowStockThreshold());
    }

    public function test_stock_and_variant_filters_are_optional(): void
    {
        $filters = new ProductFilterData();

        $this->assertNull($filters->stockFilter);
        $this->assertNull($filters->colorId);
        $this->assertNull($filters->sizeId);
    }
}
