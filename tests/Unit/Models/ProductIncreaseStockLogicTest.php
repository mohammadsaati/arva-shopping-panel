<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductIncreaseStockLogicTest extends TestCase
{
    public function test_increase_stock_skips_non_positive_quantity(): void
    {
        $product = new Product();
        $product->stock = 4;
        $product->syncOriginal();

        $product->increaseStock(0);

        $this->assertSame(4, $product->stock);
    }
}
