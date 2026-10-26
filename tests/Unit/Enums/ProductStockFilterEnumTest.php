<?php

namespace Tests\Unit\Enums;

use App\Enums\ProductStockFilterEnum;
use PHPUnit\Framework\TestCase;

class ProductStockFilterEnumTest extends TestCase
{
    public function test_default_low_threshold_is_five(): void
    {
        $this->assertSame(5, ProductStockFilterEnum::DEFAULT_LOW_THRESHOLD);
        $this->assertSame(5, ProductStockFilterEnum::LOW_STOCK->defaultLowThreshold());
    }

    public function test_out_of_stock_includes_only_zero(): void
    {
        $this->assertTrue(ProductStockFilterEnum::OUT_OF_STOCK->includesOnlyZero());
        $this->assertFalse(ProductStockFilterEnum::LOW_STOCK->includesOnlyZero());
        $this->assertFalse(ProductStockFilterEnum::IN_STOCK->includesOnlyZero());
    }

    public function test_low_stock_band_flag(): void
    {
        $this->assertTrue(ProductStockFilterEnum::LOW_STOCK->isLowStockBand());
        $this->assertFalse(ProductStockFilterEnum::IN_STOCK->isLowStockBand());
        $this->assertFalse(ProductStockFilterEnum::OUT_OF_STOCK->isLowStockBand());
    }

    public function test_color_matches_stock_state(): void
    {
        $this->assertSame('success', ProductStockFilterEnum::IN_STOCK->color());
        $this->assertSame('warning', ProductStockFilterEnum::LOW_STOCK->color());
        $this->assertSame('danger', ProductStockFilterEnum::OUT_OF_STOCK->color());
    }
}
