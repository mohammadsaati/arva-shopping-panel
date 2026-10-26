<?php

namespace App\Models;

use App\Enums\PaymentCurrencyEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\TransactionReasonTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

/**
 * @property int $id
 * @property int $user_id
 * @property TransactionReasonTypeEnum $reason_type
 * @property int|null $reason_id
 * @property int|null $payment_gateway_id
 * @property int $amount
 * @property PaymentCurrencyEnum $currency
 * @property PaymentStatusEnum $status
 * @property string|null $authority
 * @property string|null $transaction_id
 * @property string|null $card_pan
 * @property Carbon|null $paid_at
 * @property array<string, mixed>|null $meta
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read PaymentGateway|null $paymentGateway
 */
#[Guarded('id')]
class Transaction extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentGateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    protected function casts(): array
    {
        return [
            'reason_type' => TransactionReasonTypeEnum::class,
            'reason_id' => 'integer',
            'amount' => 'integer',
            'currency' => PaymentCurrencyEnum::class,
            'status' => PaymentStatusEnum::class,
            'paid_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public static function createForOrderPayment(Order $order, User $user, PaymentGateway $gateway): self
    {
        $transaction = new self();
        $transaction->user_id = $user->id;
        $transaction->reason_type = TransactionReasonTypeEnum::ORDER;
        $transaction->reason_id = $order->id;
        $transaction->payment_gateway_id = $gateway->id;
        $transaction->amount = $order->amount;
        $transaction->currency = $gateway->currency();
        $transaction->status = PaymentStatusEnum::PENDING;
        $transaction->save();

        return $transaction;
    }

    public static function latestForOrder(int $orderId): ?self
    {
        return self::query()
            ->where('reason_type', TransactionReasonTypeEnum::ORDER->value)
            ->where('reason_id', $orderId)
            ->orderByDesc('id')
            ->first();
    }

    public static function findByAuthority(string $authority): ?self
    {
        if ($authority === '') {
            return null;
        }

        return self::query()
            ->where('authority', $authority)
            ->with(['paymentGateway', 'user'])
            ->first();
    }

    public static function lockByAuthority(string $authority): ?self
    {
        if ($authority === '') {
            return null;
        }

        return self::query()
            ->where('authority', $authority)
            ->lockForUpdate()
            ->first();
    }

    public static function preparePayableForOrder(Order $order, User $user): self
    {
        $latest = self::latestForOrder($order->id);

        if ($latest !== null && $latest->status === PaymentStatusEnum::PENDING && $latest->authority === null) {
            return $latest;
        }

        $gatewayId = $latest?->payment_gateway_id;

        if ($gatewayId === null) {
            throw ValidationException::withMessages([
                'payment_gateway_id' => 'برای این سفارش درگاه پرداختی ثبت نشده است.',
            ]);
        }

        $gateway = PaymentGateway::findActive($gatewayId);

        if ($gateway === null) {
            throw ValidationException::withMessages([
                'payment' => 'درگاه پرداخت این سفارش دیگر فعال نیست.',
            ]);
        }

        return self::createForOrderPayment($order, $user, $gateway);
    }

    public function order(): ?Order
    {
        if ($this->reason_type !== TransactionReasonTypeEnum::ORDER || $this->reason_id === null) {
            return null;
        }

        return Order::query()->find($this->reason_id);
    }

    public function recordAuthority(string $authority): void
    {
        $this->authority = $authority;
        $this->status = PaymentStatusEnum::PENDING;
        $this->save();
    }

    public function markPaid(?string $transactionId = null, ?string $cardPan = null): void
    {
        if ($this->status === PaymentStatusEnum::PAID) {
            return;
        }

        $this->status = PaymentStatusEnum::PAID;
        $this->transaction_id = $transactionId;
        $this->card_pan = $cardPan;
        $this->paid_at = $this->paid_at ?? now();
        $this->save();
    }

    public function markFailed(): void
    {
        if ($this->status === PaymentStatusEnum::PAID) {
            return;
        }

        $this->status = PaymentStatusEnum::FAILED;
        $this->save();
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatusEnum::PAID;
    }
}
