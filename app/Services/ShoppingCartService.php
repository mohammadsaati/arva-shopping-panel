<?php

namespace App\Services;

use App\DTO\Cart\ShoppingCartItemAddData;
use App\DTO\Cart\ShoppingCartUpdateData;
use App\Models\Product;
use App\Models\ProductCity;
use App\Models\CustomerAddress;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\VendorProduct;
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

            $offer = VendorProduct::selectedForCart($product->id, $data->vendorProductId, lock: true);

            if ($data->vendorProductId !== null && $offer === null) {
                throw ValidationException::withMessages(['vendor_product_id' => 'فروشنده انتخاب‌شده در دسترس نیست.']);
            }

            $quantity = ($cartItem?->count ?? 0) + $data->quantity;

            if ($quantity > min($product->stock, $offer?->stock ?? $product->stock)) {
                throw ValidationException::withMessages([
                    'cart' => 'تعداد درخواستی بیشتر از موجودی این محصول است.',
                ]);
            }

            if ($cartItem === null) {
                ShoppingCartItem::createForCart($cart, $product, $quantity, $offer);
            } else {
                $cartItem->selectOffer($offer, $quantity);
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

                if ($cartItem->vendor_product_id !== null) {
                    $offer = VendorProduct::findForCart($productId, $cartItem->vendor_product_id, lock: true);

                    if ($offer === null || $quantity > $offer->stock) {
                        throw ValidationException::withMessages(['cart' => 'موجودی فروشنده برای یکی از کالاها کافی نیست.']);
                    }
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

    public function shippingForCart(ShoppingCart $cart, int $cityId): int
    {
        $productIds = array_map(static fn (Product $product): int => $product->id, $cart->productsForShipping());
        $cityPrices = ProductCity::pricesForProductsAndCities($productIds, [$cityId]);

        return $this->highestShippingRate($cart, $cityPrices[$cityId] ?? []);
    }

    /**
     * @param Collection<int, CustomerAddress> $addresses
     * @return array<int, int>
     */
    public function shippingQuotesForAddresses(ShoppingCart $cart, Collection $addresses): array
    {
        $productIds = array_map(static fn (Product $product): int => $product->id, $cart->productsForShipping());
        $cityIds = $addresses->pluck('city_id')->unique()->values()->all();
        $cityPrices = ProductCity::pricesForProductsAndCities($productIds, $cityIds);
        $quotes = [];

        foreach ($addresses as $address) {
            $quotes[$address->id] = $this->highestShippingRate($cart, $cityPrices[$address->city_id] ?? []);
        }

        return $quotes;
    }

    /**
     * @param array<int, int> $cityPrices Product ID to shipping price.
     */
    private function highestShippingRate(ShoppingCart $cart, array $cityPrices): int
    {
        $shipping = 0;

        foreach ($cart->items as $item) {
            $product = $item->product;
            $shipping = max($shipping, $item->vendorProduct?->shipping_price ?? ($cityPrices[$product->id] ?? $product->shipping_price));
        }

        return $shipping;
    }
}
