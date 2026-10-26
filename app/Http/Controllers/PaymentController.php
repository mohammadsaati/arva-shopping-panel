<?php

namespace App\Http\Controllers;

use App\Http\Requests\ZarinpalCallbackRequest;
use App\Models\User;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\PaymentServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentServiceInterface $payments,
        private readonly OrderServiceInterface $orders,
    ) {
    }

    public function start(Request $request, int $orderId): RedirectResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()
                ->guest(route('login'))
                ->withErrors(['auth' => 'برای پرداخت سفارش ابتدا وارد حساب کاربری شوید.']);
        }

        try {
            return redirect()->away($this->payments->startForUser($user, $orderId));
        } catch (ValidationException $exception) {
            return redirect()
                ->route('orders.show', ['orderId' => $orderId])
                ->withErrors($exception->errors());
        }
    }

    public function zarinpalCallback(ZarinpalCallbackRequest $request): RedirectResponse
    {
        try {
            $order = $this->payments->handleZarinpalCallback(
                authority: $request->string('Authority')->value(),
                status: $request->string('Status')->value(),
            );

            return redirect()->to($this->signedPaymentResultUrl($order->id, 'success'));
        } catch (ValidationException $exception) {
            $message = $exception->errors()['payment'][0] ?? 'پرداخت تأیید نشد.';
            $orderId = $this->orderIdFromPaymentError($exception);

            if ($orderId !== null) {
                return redirect()->to($this->signedPaymentResultUrl($orderId, 'failed'))
                    ->with('payment_error', $message);
            }

            return redirect()
                ->route('account.orders.index')
                ->withErrors(['payment' => $message]);
        }
    }

    public function result(Request $request): View|RedirectResponse
    {
        if (! $request->hasValidSignature()) {
            return redirect()
                ->route('home')
                ->withErrors(['payment' => 'لینک نتیجه پرداخت منقضی یا نامعتبر است. از بخش سفارش‌های حساب کاربری وضعیت را ببینید.']);
        }

        $orderId = $request->integer('orderId');
        $outcome = $request->string('outcome')->value();

        $user = Auth::user();
        if ($user instanceof User) {
            try {
                $this->orders->details($user, $orderId);
                $redirect = redirect()->route('orders.show', ['orderId' => $orderId]);

                return $outcome === 'success'
                    ? $redirect->with('success', 'پرداخت سفارش با موفقیت تأیید شد.')
                    : $redirect->withErrors([
                        'payment' => $request->session()->get('payment_error', 'پرداخت تأیید نشد.'),
                    ]);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
                // کاربر دیگری وارد شده؛ صفحه عمومی نتیجه را نشان بده.
            }
        }

        return view('payments.result', [
            'orderId' => $orderId,
            'outcome' => $outcome,
            'loginUrl' => route('login'),
        ]);
    }

    private function signedPaymentResultUrl(int $orderId, string $outcome): string
    {
        return URL::temporarySignedRoute(
            'payments.result',
            now()->addHours(2),
            ['orderId' => $orderId, 'outcome' => $outcome],
        );
    }

    private function orderIdFromPaymentError(ValidationException $exception): ?int
    {
        $orderId = $exception->errors()['order_id'][0] ?? null;

        return is_numeric($orderId) ? (int) $orderId : null;
    }
}
