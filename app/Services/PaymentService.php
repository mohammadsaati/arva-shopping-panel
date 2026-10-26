<?php

namespace App\Services;

use App\Enums\PaymentGatewayDriverEnum;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Contracts\ZarinpalClientInterface;
use App\Support\PaymentGatewayActivation;
use Illuminate\Validation\ValidationException;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(private readonly ZarinpalClientInterface $zarinpal)
    {
    }

    public function start(Order $order): string
    {
        if ($order->isPaid()) {
            throw ValidationException::withMessages([
                'payment' => 'این سفارش قبلاً پرداخت شده است.',
            ]);
        }

        if ($order->amount < 1) {
            $order->markPaid();

            return route('orders.show', ['orderId' => $order->id]);
        }

        $user = User::query()->findOrFail($order->user_id);
        $transaction = Transaction::preparePayableForOrder($order, $user);
        $gateway = $transaction->paymentGateway;

        if ($gateway === null) {
            throw ValidationException::withMessages([
                'payment_gateway_id' => 'برای این سفارش درگاه پرداختی انتخاب نشده است.',
            ]);
        }

        return match ($gateway->driver) {
            PaymentGatewayDriverEnum::ZARINPAL => $this->startZarinpal(
                $order,
                $transaction,
                $gateway->merchantId(),
                $gateway->usesSandbox(),
                $gateway->currency()->value,
            ),
        };
    }

    public function startForUser(User $user, int $orderId): string
    {
        $order = Order::detailsForUser($user->id, $orderId);

        if (! $order->canRetryPayment() && ! $order->isPaid()) {
            throw ValidationException::withMessages([
                'payment' => 'امکان پرداخت مجدد این سفارش وجود ندارد.',
            ]);
        }

        return $this->start($order);
    }

    public function handleZarinpalCallback(string $authority, string $status): Order
    {
        $transaction = Transaction::findByAuthority($authority);

        if ($transaction === null) {
            throw $this->paymentError('سفارش متناظر با این پرداخت پیدا نشد.');
        }

        $order = $this->orderForTransaction($transaction);

        if ($order === null) {
            throw $this->paymentError('سفارش متناظر با این پرداخت پیدا نشد.');
        }

        if ($order->isPaid()) {
            return Order::detailsForUser($order->user_id, $order->id);
        }

        if (strtoupper($status) !== 'OK') {
            $transaction->markFailed();
            $order->markPaymentFailed();

            throw $this->paymentError(
                'پرداخت لغو شد یا ناتمام ماند. می‌توانید دوباره تلاش کنید.',
                $order,
            );
        }

        $gateway = $transaction->paymentGateway;

        if ($gateway === null || $gateway->driver !== PaymentGatewayDriverEnum::ZARINPAL) {
            $transaction->markFailed();
            $order->markPaymentFailed();

            throw $this->paymentError('درگاه این پرداخت زرین‌پال نیست.', $order);
        }

        $result = $this->zarinpal->verifyPayment(
            merchantId: $gateway->merchantId(),
            amount: $transaction->amount,
            authority: $authority,
            sandbox: $gateway->usesSandbox(),
        );

        if (! $result->verified) {
            $transaction->markFailed();
            $order->markPaymentFailed();

            throw $this->paymentError($result->message, $order);
        }

        return Order::runInTransaction(function () use ($order, $transaction, $result): Order {
            $lockedTransaction = Transaction::lockByAuthority((string) $transaction->authority);

            if ($lockedTransaction === null) {
                throw ValidationException::withMessages([
                    'payment' => 'تراکنش متناظر با این پرداخت پیدا نشد.',
                ]);
            }

            $lockedOrder = Order::query()->whereKey($order->id)->lockForUpdate()->firstOrFail();

            if (! $lockedTransaction->isPaid()) {
                $lockedTransaction->markPaid(
                    transactionId: $result->refId !== null ? (string) $result->refId : null,
                    cardPan: $result->cardPan,
                );
            }

            if (! $lockedOrder->isPaid()) {
                $lockedOrder->markPaid();
            }

            return Order::detailsForUser($lockedOrder->user_id, $lockedOrder->id);
        });
    }

    private function startZarinpal(
        Order $order,
        Transaction $transaction,
        string $merchantId,
        bool $sandbox,
        string $currency,
    ): string {
        if (! PaymentGatewayActivation::isValidMerchantId($merchantId)) {
            throw ValidationException::withMessages([
                'payment' => 'کد پذیرنده زرین‌پال در تنظیمات درگاه کامل نیست.',
            ]);
        }

        $result = $this->zarinpal->requestPayment(
            merchantId: $merchantId,
            amount: $transaction->amount,
            callbackUrl: route('payments.zarinpal.callback'),
            description: 'پرداخت سفارش شماره '.$order->id,
            currency: $currency,
            sandbox: $sandbox,
            metadata: array_filter([
                'mobile' => $order->phone_number,
                'order_id' => (string) $order->id,
            ]),
        );

        if (! $result->ok || $result->authority === null) {
            $transaction->markFailed();
            $order->markPaymentFailed();

            throw ValidationException::withMessages([
                'payment' => $result->message,
            ]);
        }

        $transaction->recordAuthority($result->authority);

        return $this->zarinpal->startPayUrl($result->authority, $sandbox);
    }

    private function orderForTransaction(Transaction $transaction): ?Order
    {
        if ($transaction->reason_id === null) {
            return null;
        }

        return Order::query()->find($transaction->reason_id);
    }

    private function paymentError(string $message, ?Order $order = null): ValidationException
    {
        $errors = ['payment' => $message];

        if ($order !== null) {
            $errors['order_id'] = [(string) $order->id];
        }

        return ValidationException::withMessages($errors);
    }
}
