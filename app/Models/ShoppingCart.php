<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Closure;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int|null $customer_id
 * @property string|null $shopping_key
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */

#[Guarded('id')]
class ShoppingCart extends Model
{
    use HasFactory;

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingCartItem::class);
    }

    public static function forShoppingKey(string $shoppingKey): ?self
    {
        return self::query()
            ->where('shopping_key', $shoppingKey)
            ->with(['items.product.item', 'items.product.color', 'items.product.size'])
            ->first();
    }

    public static function itemCountForShoppingKey(string $shoppingKey): int
    {
        $cart = self::query()->where('shopping_key', $shoppingKey)->first();

        return $cart?->items()->sum('count') ?? 0;
    }

    public static function runInTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    public static function lockOrCreateForShoppingKey(string $shoppingKey): self
    {
        $cart = self::query()
            ->where('shopping_key', $shoppingKey)
            ->lockForUpdate()
            ->first();

        if ($cart !== null) {
            return $cart;
        }

        return self::query()->firstOrCreate(['shopping_key' => $shoppingKey]);
    }

    public static function lockForShoppingKey(string $shoppingKey): ?self
    {
        return self::query()
            ->where('shopping_key', $shoppingKey)
            ->lockForUpdate()
            ->first();
    }

    public function subtotal(): int
    {
        return $this->loadedItems()->sum(
            fn (ShoppingCartItem $item): int => $item->product->price * $item->count,
        );
    }

    /** @return array<int, Product> */
    public function productsForShipping(): array
    {
        return $this->loadedItems()->map(
            fn (ShoppingCartItem $item): Product => $item->product,
        )->all();
    }

    /** @return Collection<int, ShoppingCartItem> */
    public function discountItems(): Collection
    {
        $this->loadMissing('items.product.item');

        return $this->getRelation('items');
    }

    /** @return Collection<int, ShoppingCartItem> */
    private function loadedItems(): Collection
    {
        if (! $this->relationLoaded('items')) {
            $this->load('items.product');
        }

        return $this->getRelation('items');
    }
}
