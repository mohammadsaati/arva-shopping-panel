<?php

namespace App\Services\Contracts;

use App\DTO\Cart\ShoppingCartItemAddData;
use App\DTO\Cart\ShoppingCartUpdateData;
use App\Models\ShoppingCart;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use App\Models\CustomerAddress;

interface ShoppingCartServiceInterface
{
    public function get(string $shoppingKey): ?ShoppingCart;

    public function count(string $shoppingKey): int;

    public function add(string $shoppingKey, ShoppingCartItemAddData $data): ShoppingCart;

    public function update(string $shoppingKey, ShoppingCartUpdateData $data): void;

    public function remove(string $shoppingKey, int $productId): void;

    /** @param array<int, Product> $products */
    public function shippingForProducts(array $products, int $cityId): int;

    /**
     * @param Collection<int, CustomerAddress> $addresses
     * @return array<int, int> Address ID to shipping price.
     */
    public function shippingQuotesForAddresses(ShoppingCart $cart, Collection $addresses): array;
}
