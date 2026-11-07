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
 * @property-read VendorOrder|null $vendorOrder
 * @property-read VendorProduct|null $vendorProduct
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

    public function vendorOrder(): BelongsTo
    {
        return $this->belongsTo(VendorOrder::class);
    }

    public function vendorProduct(): BelongsTo
    {
        return $this->belongsTo(VendorProduct::class);
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

    public static function createForOrder(
        Order $order,
        Product $product,
        int $count,
        int $unitPrice,
        int $discountAmount = 0,
        ?VendorProduct $offer = null,
        ?VendorOrder $vendorOrder = null,
    ): self
    {
        $totalPrice = $unitPrice * $count;
        $netAmount = $totalPrice - $discountAmount;
        $rate = $offer?->vendor->commission?->rate_bps ?? 0;
        $commission = intdiv($netAmount * $rate + 5000, 10000);
        $item = new self();
        $item->order_id = $order->id;
        $item->product_id = $product->id;
        $item->count = $count;
        $item->vendor_order_id = $vendorOrder?->id;
        $item->vendor_product_id = $offer?->id;
        $item->unit_price = $unitPrice;
        $item->off_price = null;
        $item->total_price = $totalPrice;
        $item->commission_rate_bps = $rate;
        $item->commission_amount = $commission;
        $item->payable_amount = $netAmount - $commission;
        $item->save();

        $vendorOrder?->addLine($totalPrice, $discountAmount, $commission);

        return $item;
    }
}
