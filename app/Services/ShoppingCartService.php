<?php

namespace App\Services;

use App\DTO\Cart\ShoppingCartItemAddData;
use App\DTO\Cart\ShoppingCartUpdateData;
use App\Models\Product;
use App\Models\ProductCity;
use App\Models\CustomerAddress;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Services\Contracts\ShoppingCartServiceInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\Collection;

class ShoppingCartService implements ShoppingCartServiceInterface
{
    public function get(string $shoppingKey): ?ShoppingCart
    {
        return ShoppingCart::forShoppingKey($shoppingKey);
    }

    public function count(string $shoppingKey): int
    {
        return ShoppingCart::itemCountForShoppingKey($shoppingKey);
    }

    public function add(string $shoppingKey, ShoppingCartItemAddData $data): ShoppingCart
    {
        if ($data->quantity < 1) {
            throw ValidationException::withMessages(['quantity' => 'تعداد باید حداقل یک باشد.']);
        }

        ShoppingCart::runInTransaction(function () use ($shoppingKey, $data): void {
            $cart = ShoppingCart::lockOrCreateForShoppingKey($shoppingKey);
            $cartItem = ShoppingCartItem::forCartProduct($cart->id, $data->productId, lock: true);
            $product = Product::findPurchasableForCart($data->productId, lock: true);

            if ($product === null) {
                throw ValidationException::withMessages([
                    'cart' => 'این محصول در حال حاضر برای خرید در دسترس نیست.',
                ]);
            }

            $quantity = ($cartItem?->count ?? 0) + $data->quantity;

            if ($quantity > $product->stock) {
                throw ValidationException::withMessages([
                    'cart' => 'تعداد درخواستی بیشتر از موجودی این محصول است.',
                ]);
            }

            if ($cartItem === null) {
                ShoppingCartItem::createForCart($cart, $product, $quantity);
            } else {
                $cartItem->updateQuantity($quantity);
            }
        });

        return ShoppingCart::forShoppingKey($shoppingKey)
            ?? throw ValidationException::withMessages(['cart' => 'سبد خرید ایجاد نشد.']);
    }

    public function update(string $shoppingKey, ShoppingCartUpdateData $data): void
    {
        $quantities = $data->quantities;
        ksort($quantities, SORT_NUMERIC);

        ShoppingCart::runInTransaction(function () use ($shoppingKey, $quantities): void {
            $cart = ShoppingCart::lockForShoppingKey($shoppingKey);

            if ($cart === null) {
                return;
            }

            foreach ($quantities as $productId => $quantity) {
                $productId = (int) $productId;
                $cartItem = ShoppingCartItem::forCartProduct($cart->id, $productId, lock: true);

                if ($cartItem === null) {
                    continue;
                }

                $product = Product::findPurchasableForCart($productId, lock: true);

                if ($product === null || $quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'یکی از کالاها دیگر موجود نیست یا تعداد آن از موجودی بیشتر است.',
                    ]);
                }

                $cartItem->updateQuantity($quantity);
            }
        });
    }

    public function remove(string $shoppingKey, int $productId): void
    {
        ShoppingCart::runInTransaction(function () use ($shoppingKey, $productId): void {
            $cart = ShoppingCart::lockForShoppingKey($shoppingKey);

            if ($cart !== null) {
                ShoppingCartItem::removeFromCart($cart->id, $productId);
            }
        });
    }

    /** @param array<int, Product> $products */
    public function shippingForProducts(array $products, int $cityId): int
    {
        $productIds = array_map(static fn (Product $product): int => $product->id, $products);
        $cityPrices = ProductCity::pricesForProductsAndCities($productIds, [$cityId]);

        return $this->highestShippingRate($products, $cityPrices[$cityId] ?? []);
    }

    /**
     * @param Collection<int, CustomerAddress> $addresses
     * @return array<int, int>
     */
    public function shippingQuotesForAddresses(ShoppingCart $cart, Collection $addresses): array
    {
        $products = $cart->productsForShipping();
        $productIds = array_map(static fn (Product $product): int => $product->id, $products);
        $cityIds = $addresses->pluck('city_id')->unique()->values()->all();
        $cityPrices = ProductCity::pricesForProductsAndCities($productIds, $cityIds);
        $quotes = [];

        foreach ($addresses as $address) {
            $quotes[$address->id] = $this->highestShippingRate($products, $cityPrices[$address->city_id] ?? []);
        }

        return $quotes;
    }

    /**
     * @param array<int, Product> $products
     * @param array<int, int> $cityPrices Product ID to shipping price.
     */
    private function highestShippingRate(array $products, array $cityPrices): int
    {
        $shipping = 0;

        foreach ($products as $product) {
            $shipping = max($shipping, $cityPrices[$product->id] ?? $product->shipping_price);
        }

        return $shipping;
    }
}
