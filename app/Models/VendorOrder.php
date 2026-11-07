<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\DTO\VendorPanel\VendorOrderFilterData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $order_id
 * @property int $vendor_id
 * @property string $vendor_name
 * @property int $subtotal_amount
 * @property int $discount_amount
 * @property int $shipping_amount
 * @property int $total_amount
 * @property int $commission_amount
 * @property int $payable_amount
 * @property OrderStatusEnum $status
 * @property string|null $tracking_code
 * @property Carbon|null $shipped_at
 * @property Carbon|null $delivered_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Order $order
 * @property-read Vendor $vendor
 */
#[Guarded('id')]
class VendorOrder extends Model
{
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected function casts(): array
    {
        return [
            'subtotal_amount' => 'integer',
            'discount_amount' => 'integer',
            'shipping_amount' => 'integer',
            'total_amount' => 'integer',
            'commission_amount' => 'integer',
            'payable_amount' => 'integer',
            'status' => OrderStatusEnum::class,
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public static function filterForVendor(int $vendorId, VendorOrderFilterData $filters): Paginator
    {
        $sorts = array_intersect_key($filters->sorts, array_flip(['id', 'total_amount', 'created_at']));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];
        $query = self::query()->where('vendor_id', $vendorId)
            ->with('order.user')
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $builder) use ($filters): void {
                $search = trim($filters->search);
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->whereHas('order.user', fn (Builder $user) => $user
                        ->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%"));

                    if (ctype_digit($search)) {
                        $builder->orWhere('order_id', (int) $search);
                    }
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function detailsForVendor(int $vendorId, int $vendorOrderId): self
    {
        return self::query()->where('vendor_id', $vendorId)
            ->with(['order.user', 'items.product.item', 'items.product.color', 'items.product.size'])
            ->findOrFail($vendorOrderId);
    }

    public static function countForVendor(int $vendorId): int
    {
        return self::query()->where('vendor_id', $vendorId)->count();
    }

    public static function createForOrder(Order $order, Vendor $vendor): self
    {
        $vendorOrder = new self();
        $vendorOrder->order_id = $order->id;
        $vendorOrder->vendor_id = $vendor->id;
        $vendorOrder->vendor_name = $vendor->company_name;
        $vendorOrder->subtotal_amount = 0;
        $vendorOrder->discount_amount = 0;
        $vendorOrder->shipping_amount = 0;
        $vendorOrder->total_amount = 0;
        $vendorOrder->commission_amount = 0;
        $vendorOrder->payable_amount = 0;
        $vendorOrder->status = OrderStatusEnum::PENDING;
        $vendorOrder->save();

        return $vendorOrder;
    }

    public function addLine(int $subtotal, int $discount, int $commission): void
    {
        $this->subtotal_amount += $subtotal;
        $this->discount_amount += $discount;
        $this->total_amount += $subtotal - $discount;
        $this->commission_amount += $commission;
        $this->payable_amount += $subtotal - $discount - $commission;
        $this->save();
    }

    public static function syncStatusForOrder(Order $order, OrderStatusEnum $status): void
    {
        self::query()->where('order_id', $order->id)->update(['status' => $status->value]);
    }
}
