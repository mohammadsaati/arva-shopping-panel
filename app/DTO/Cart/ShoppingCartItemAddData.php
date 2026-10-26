<?php

namespace App\DTO\Cart;

final readonly class ShoppingCartItemAddData
{
    public function __construct(
        public int $productId,
        public int $quantity = 1,
    ) {
    }
}
