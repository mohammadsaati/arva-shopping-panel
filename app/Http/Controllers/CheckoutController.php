<?php

namespace App\Http\Controllers;

use App\DTO\Order\PlaceOrderData;
use App\DTO\Discount\DiscountQuoteRequestData;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\User;
use App\Services\Contracts\CustomerAddressServiceInterface;
use App\Services\Contracts\DiscountServiceInterface;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\PaymentGatewayServiceInterface;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Contracts\ShoppingCartServiceInterface;
use App\Support\ShoppingCartSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly ShoppingCartServiceInterface $shoppingCart,
        private readonly OrderServiceInterface $orders,
        private readonly CustomerAddressServiceInterface $addresses,
        private readonly DiscountServiceInterface $discounts,
        private readonly PaymentGatewayServiceInterface $paymentGateways,
        private readonly PaymentServiceInterface $payments,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->shoppingCart->get(ShoppingCartSession::key($request));

        if ($cart === null || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'برای ادامه، ابتدا محصولی به سبد خرید اضافه کنید.',
            ]);
        }

        $user = $request->user();

        if (! $user instanceof User) {
            return redirect()
                ->guest(route('login'))
                ->withErrors(['auth' => 'برای تکمیل خرید ابتدا وارد حساب کاربری شوید.']);
        }

        $addresses = $this->addresses->checkoutOptions($user);
        $discountQuote = null;
        $discountCode = $request->session()->get('discount_code');

        if (is_string($discountCode) && $discountCode !== '') {
            try {
                $discountQuote = $this->discounts->quoteForCart(new DiscountQuoteRequestData(code: $discountCode), $cart, $user);
            } catch (ValidationException $exception) {
                $request->session()->forget('discount_code');
                $request->session()->flash(
                    'discount_error',
                    $exception->errors()['discount_code'][0] ?? 'کد تخفیف دیگر برای این سبد خرید معتبر نیست.',
                );
            }
        }

        return view('checkout.show', [
            'cart' => $cart,
            'addresses' => $addresses,
            'shippingQuotes' => $this->shoppingCart->shippingQuotesForAddresses($cart, $addresses),
            'discountQuote' => $discountQuote,
            'paymentGateways' => $this->paymentGateways->activeForCheckout(),
        ]);
    }

    public function store(PlaceOrderRequest $request): RedirectResponse
    {
        $order = $this->orders->place(
            $request->user(),
            ShoppingCartSession::key($request),
            new PlaceOrderData(
                addressId: $request->integer('address_id'),
                discountCode: is_string($request->session()->get('discount_code'))
                    ? $request->session()->get('discount_code')
                    : null,
                paymentGatewayId: $request->filled('payment_gateway_id')
                    ? $request->integer('payment_gateway_id')
                    : null,
            ),
        );

        $request->session()->forget('discount_code');

        if ($order->isPaid()) {
            return redirect()
                ->route('orders.show', ['orderId' => $order->id])
                ->with('success', 'سفارش شما با موفقیت ثبت شد.');
        }

        try {
            return redirect()->away($this->payments->start($order));
        } catch (ValidationException $exception) {
            return redirect()
                ->route('orders.show', ['orderId' => $order->id])
                ->withErrors($exception->errors())
                ->with('success', 'سفارش ثبت شد؛ برای تکمیل، پرداخت را دوباره شروع کنید.');
        }
    }
}
