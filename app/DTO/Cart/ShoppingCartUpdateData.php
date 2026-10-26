<?php

namespace App\DTO\Cart;

final readonly class ShoppingCartUpdateData
{
    /**
     * @param array<int|string, int> $quantities
     */
    public function __construct(public array $quantities)
    {
    }
}
