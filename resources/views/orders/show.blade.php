<x-user-panel title="جزئیات سفارش" :sub-title="session('success', 'وضعیت فعلی و جزئیات سفارش شما.')">
    <article class="store-order-confirmation">
        <div class="store-order-confirmation__header">
            <span class="store-order-confirmation__icon" aria-hidden="true"><i class="ki-filled ki-basket"></i></span>
            <div>
                <h2>سفارش شمارهٔ {{ $order->id }}</h2>
                <p>تاریخ ثبت: {{ jalali_date($order->created_at, 'Y/m/d H:i') }}</p>
            </div>
        </div>

        <div class="mb-5 flex flex-wrap items-center gap-3">
            <span>وضعیت سفارش:</span>
            <x-badge :variant="$order->status->color()" size="sm" dot>{{ $order->status->trans() }}</x-badge>
            <span>وضعیت پرداخت:</span>
            <x-badge :variant="$order->payment_status->color()" size="sm" dot>{{ $order->payment_status->trans() }}</x-badge>
        </div>

        @error('payment')
            <div class="user-account-alert" role="alert">{{ $message }}</div>
        @enderror
        @error('payment_gateway_id')
            <div class="user-account-alert" role="alert">{{ $message }}</div>
        @enderror

        @php($payment = $order->resolvedPaymentTransaction())
        @if ($payment?->paymentGateway)
            <div class="store-order-gateway">
                <img src="{{ public_image_url($payment->paymentGateway->displayImage()) }}" alt="" width="48" height="48">
                <div>
                    <strong>{{ $payment->paymentGateway->name }}</strong>
                    <small>{{ $payment->paymentGateway->driver->trans() }}</small>
                </div>
            </div>
        @endif

        @if ($payment?->transaction_id)
            <p class="mb-5 text-sm text-gray-600">شماره پیگیری پرداخت: <span dir="ltr">{{ $payment->transaction_id }}</span></p>
        @endif

        @if ($order->full_address)
            <div class="store-order-confirmation__address">
                <h3>نشانی تحویل</h3>
                <p>{{ $order->recipient_name }} · {{ $order->phone_number }}</p>
                <p>{{ $order->province }}، {{ $order->city }}، {{ $order->full_address }}</p>
            </div>
        @endif

        <div class="store-cart-table-wrap">
            <table class="store-cart-table store-order-table">
                <thead>
                    <tr><th scope="col">محصول</th><th scope="col">قیمت واحد</th><th scope="col">تعداد</th><th scope="col">جمع</th></tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $orderItem)
                        <tr>
                            <td>{{ $orderItem->product->item->name }}</td>
                            <td>{{ number_format($orderItem->unit_price) }} تومان</td>
                            <td>{{ $orderItem->count }}</td>
                            <td>{{ number_format($orderItem->total_price) }} تومان</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <dl class="store-order-totals">
            @if ($order->off_amount > 0)
                <div><dt>کد تخفیف <span dir="ltr">{{ $order->discount_code }}</span></dt><dd>−{{ number_format($order->off_amount) }} تومان</dd></div>
            @endif
            <div><dt>هزینه ارسال</dt><dd>{{ $order->shipping_amount > 0 ? number_format($order->shipping_amount) . ' تومان' : 'رایگان' }}</dd></div>
            <div><dt>مبلغ سفارش</dt><dd>{{ number_format($order->amount) }} تومان</dd></div>
        </dl>

        <div class="flex flex-wrap gap-3">
            @if ($order->canRetryPayment())
                <form method="POST" action="{{ route('orders.pay', $order->id) }}">
                    @csrf
                    <x-button type="submit" icon="ki-filled ki-wallet">پرداخت آنلاین</x-button>
                </form>
            @endif
            <x-button href="{{ route('account.orders.index') }}" variant="light" icon="ki-filled ki-basket">سفارش‌های من</x-button>
            <x-button href="{{ route('shop.index') }}" icon="ki-filled ki-shop">ادامه خرید</x-button>
        </div>
    </article>
</x-user-panel>
