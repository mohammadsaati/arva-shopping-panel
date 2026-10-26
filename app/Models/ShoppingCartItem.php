<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $shopping_cart_id
 * @property int $product_id
 * @property int|null $vendor_product_id
 * @property int $count
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read ShoppingCart $cart
 * @property-read Product $product
 */

#[Guarded('id')]
class ShoppingCartItem extends Model
{
    use HasFactory;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(ShoppingCart::class, 'shopping_cart_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function casts(): array
    {
        return [
            'vendor_product_id' => 'integer',
            'count' => 'integer',
        ];
    }

    public static function removeFromCart(int $cartId, int $productId): void
    {
        self::query()
            ->where('shopping_cart_id', $cartId)
            ->where('product_id', $productId)
            ->delete();
    }

    public static function forCartProduct(int $cartId, int $productId, bool $lock = false): ?self
    {
        $query = self::query()
            ->where('shopping_cart_id', $cartId)
            ->where('product_id', $productId);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /** @return Collection<int, self> */
    public static function lockForCart(int $cartId): Collection
    {
        return self::query()
            ->where('shopping_cart_id', $cartId)
            ->orderBy('product_id')
            ->lockForUpdate()
            ->get();
    }

    public static function createForCart(ShoppingCart $cart, Product $product, int $quantity): self
    {
        $item = new self();
        $item->shopping_cart_id = $cart->id;
        $item->product_id = $product->id;
        $item->count = $quantity;
        $item->save();

        return $item;
    }

    public function updateQuantity(int $quantity): void
    {
        $this->count = $quantity;
        $this->save();
    }

    public static function clearCart(int $cartId): void
    {
        self::query()->where('shopping_cart_id', $cartId)->delete();
    }
}
