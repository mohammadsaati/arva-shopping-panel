<?php

namespace App\Models;

use App\DTO\Order\OrderFilterData;
use App\DTO\Order\OrderHistoryFilterData;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\TransactionReasonTypeEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Closure;

/**
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 * @property int|null $discount_id
 * @property string|null $discount_code
 * @property int $off_amount
 * @property int $shipping_amount
 * @property int $amount
 * @property OrderStatusEnum $status
 * @property PaymentStatusEnum $payment_status
 * @property Carbon|null $paid_at
 * @property bool $inventory_restored
 * @property string|null $tracking_code
 * @property string|null $recipient_name
 * @property string|null $phone_number
 * @property string|null $province
 * @property string|null $city
 * @property string|null $full_address
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read CustomerAddress $address
 * @property-read Discount|null $discount
 * @property-read \Illuminate\Database\Eloquent\Collection<int, OrderItem> $items
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Transaction> $paymentTransactions
 */
#[Guarded('id')]
class Order extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'reason_id')
            ->where('reason_type', TransactionReasonTypeEnum::ORDER->value);
    }

    protected function casts(): array
    {
        return [
            'off_amount' => 'integer',
            'shipping_amount' => 'integer',
            'amount' => 'integer',
            'status' => OrderStatusEnum::class,
            'payment_status' => PaymentStatusEnum::class,
            'paid_at' => 'datetime',
            'inventory_restored' => 'boolean',
        ];
    }

    public static function filterForAdmin(OrderFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'amount', 'status', 'payment_status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with('user')
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $query) use ($filters): void {
                $search = trim($filters->search);

                $query->where(function (Builder $query) use ($search): void {
                    $query->where('recipient_name', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhereHas('user', function (Builder $user) use ($search): void {
                            $user->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });

                    if (ctype_digit($search)) {
                        $query->orWhere('orders.id', (int) $search);
                    }
                });
            })
            ->when($filters->status !== null, fn (Builder $query) => $query->where('status', $filters->status->value))
            ->when($filters->paymentStatus !== null, fn (Builder $query) => $query->where('payment_status', $filters->paymentStatus->value))
            ->when($filters->userId !== null, fn (Builder $query) => $query->where('user_id', $filters->userId));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function historyForUser(int $userId, OrderHistoryFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'created_at', 'amount'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->where('user_id', $userId)
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $query) use ($filters): void {
                $search = trim(strtr($filters->search, [
                    '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
                    '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
                    '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
                    '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
                ]));
                $search = ltrim($search, '# ');

                if ($search === '') {
                    return;
                }

                if (ctype_digit($search)) {
                    $query->whereKey((int) $search);
                } else {
                    $query->whereKey(0);
                }
            })
            ->when($filters->status !== null, fn (Builder $query) => $query->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function detailsForAdmin(int $orderId): self
    {
        return self::query()
            ->with([
                'user',
                'discount',
                'paymentTransactions.paymentGateway',
                'items.product.item',
                'items.product.color',
                'items.product.size',
            ])
            ->findOrFail($orderId);
    }

    public static function lockForStatusUpdate(int $orderId): self
    {
        return self::query()->whereKey($orderId)->lockForUpdate()->firstOrFail();
    }

    public static function countByStatus(OrderStatusEnum $status): int
    {
        return self::query()->where('status', $status->value)->count();
    }

    public static function dashboardPending(int $limit): Paginator
    {
        return self::query()
            ->where('status', OrderStatusEnum::PENDING->value)
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->simplePaginate(perPage: $limit, page: 1)
            ->withQueryString();
    }

    public static function runInTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    public static function createForCheckout(User $user, CustomerAddress $address): self
    {
        $order = new self();
        $order->user_id = $user->id;
        $order->address_id = $address->id;
        $order->off_amount = 0;
        $order->shipping_amount = 0;
        $order->amount = 0;
        $order->status = OrderStatusEnum::PENDING;
        $order->payment_status = PaymentStatusEnum::PENDING;
        $order->recipient_name = $address->recipient_name;
        $order->phone_number = $address->phone_number;
        $order->province = $address->province->name;
        $order->city = $address->city->name;
        $order->full_address = $address->full_address;
        $order->save();

        return $order;
    }

    public static function lockPayableForUser(int $userId, int $orderId): self
    {
        return self::query()
            ->where('user_id', $userId)
            ->whereKey($orderId)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function markPaid(): void
    {
        if ($this->isPaid()) {
            return;
        }

        $this->payment_status = PaymentStatusEnum::PAID;
        $this->paid_at = $this->paid_at ?? now();
        $this->save();

        if ($this->discount_id !== null) {
            $discount = Discount::query()->whereKey($this->discount_id)->lockForUpdate()->first();
            $discount?->recordUsage();
        }
    }

    public function markPaymentFailed(): void
    {
        if ($this->payment_status === PaymentStatusEnum::PAID) {
            return;
        }

        $this->payment_status = PaymentStatusEnum::FAILED;
        $this->save();
    }

    public function updateTotals(int $subtotal, int $shippingAmount, int $discountAmount = 0, ?Discount $discount = null): void
    {
        $this->discount_id = $discount?->id;
        $this->discount_code = $discount?->code;
        $this->off_amount = min($subtotal, max(0, $discountAmount));
        $this->shipping_amount = $shippingAmount;
        $this->amount = max(0, $subtotal - $this->off_amount) + $shippingAmount;
        $this->save();
    }

    public static function detailsForUser(int $userId, int $orderId): self
    {
        return self::query()
            ->where('user_id', $userId)
            ->whereKey($orderId)
            ->with([
                'discount',
                'paymentTransactions.paymentGateway',
                'items.product.item',
                'items.product.color',
                'items.product.size',
            ])
            ->firstOrFail();
    }

    public function updateStatus(OrderStatusEnum $status): void
    {
        if ($this->status === $status) {
            return;
        }

        if ($status->requiresPaidPayment() && ! $this->isPaid()) {
            throw ValidationException::withMessages([
                'status' => 'برای این وضعیت، ابتدا پرداخت سفارش باید تأیید شده باشد.',
            ]);
        }

        $this->status = $status;
        $this->save();

        if ($status === OrderStatusEnum::CANCELLED) {
            $this->restoreInventoryIfNeeded();
        }
    }

    public function restoreInventoryIfNeeded(): void
    {
        if ($this->inventory_restored) {
            return;
        }

        $this->loadMissing('items.product');

        foreach ($this->items as $orderItem) {
            $product = $orderItem->product;

            if ($product !== null) {
                $product->increaseStock($orderItem->count);
            }
        }

        $this->inventory_restored = true;
        $this->save();
    }

    /**
     * @return list<int>
     */
    public static function expireStaleUnpaid(int $olderThanHours): array
    {
        $cutoff = now()->subHours($olderThanHours);
        $expiredIds = [];

        self::query()
            ->where('created_at', '<', $cutoff)
            ->where('status', '!=', OrderStatusEnum::CANCELLED->value)
            ->whereIn('payment_status', [
                PaymentStatusEnum::PENDING->value,
                PaymentStatusEnum::FAILED->value,
            ])
            ->where('amount', '>', 0)
            ->orderBy('id')
            ->chunkById(50, function ($orders) use (&$expiredIds): void {
                foreach ($orders as $order) {
                    self::runInTransaction(function () use ($order, &$expiredIds): void {
                        $locked = self::query()->whereKey($order->id)->lockForUpdate()->first();

                        if ($locked === null || $locked->status === OrderStatusEnum::CANCELLED || $locked->isPaid()) {
                            return;
                        }

                        $locked->updateStatus(OrderStatusEnum::CANCELLED);
                        $expiredIds[] = $locked->id;
                    });
                }
            });

        return $expiredIds;
    }

    public function paymentStatusEnum(): ?PaymentStatusEnum
    {
        return $this->payment_status;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatusEnum::PAID;
    }

    public function canRetryPayment(): bool
    {
        return $this->amount > 0 && $this->payment_status->canRetry();
    }

    public function resolvedPaymentTransaction(): ?Transaction
    {
        $transactions = $this->relationLoaded('paymentTransactions')
            ? $this->paymentTransactions
            : $this->paymentTransactions()->with('paymentGateway')->orderByDesc('id')->get();

        return $transactions->first(fn (Transaction $transaction): bool => $transaction->status === PaymentStatusEnum::PAID)
            ?? $transactions->first();
    }

    public function itemsSubtotal(): int
    {
        return max(0, $this->amount - $this->shipping_amount + $this->off_amount);
    }
}
