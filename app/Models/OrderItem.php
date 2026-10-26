<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int|null $vendor_order_id
 * @property int|null $vendor_product_id
 * @property int $count
 * @property int $unit_price
 * @property int|null $off_price
 * @property int $total_price
 * @property int $commission_rate_bps
 * @property int $commission_amount
 * @property int $payable_amount
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Order $order
 * @property-read Product $product
 */
#[Guarded('id')]
class OrderItem extends Model
{
    use HasFactory;

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected function casts(): array
    {
        return [
            'vendor_order_id' => 'integer',
            'vendor_product_id' => 'integer',
            'count' => 'integer',
            'unit_price' => 'integer',
            'off_price' => 'integer',
            'total_price' => 'integer',
            'commission_rate_bps' => 'integer',
            'commission_amount' => 'integer',
            'payable_amount' => 'integer',
        ];
    }

    public static function createForOrder(Order $order, Product $product, int $count, int $totalPrice): self
    {
        $item = new self();
        $item->order_id = $order->id;
        $item->product_id = $product->id;
        $item->count = $count;
        $item->unit_price = $product->price;
        $item->off_price = null;
        $item->total_price = $totalPrice;
        $item->save();

        return $item;
    }
}
