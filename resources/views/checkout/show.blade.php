@php
    $selectedAddressId = (int) old('address_id', $addresses->first()?->id);
    $selectedShipping = $shippingQuotes[$selectedAddressId] ?? $shippingQuotes[$addresses->first()?->id] ?? 0;
    $lineCount = $cart->items->count();
    $unitCount = (int) $cart->items->sum('count');
    $goodsPayable = $cart->subtotal() - ($discountQuote?->discountAmount ?? 0);
    $payableAmount = $goodsPayable + ($addresses->isEmpty() ? 0 : $selectedShipping);
    $needsPayment = $payableAmount > 0;
    $selectedGatewayId = (int) old('payment_gateway_id', $paymentGateways->first()?->id);
    $canPlace = $addresses->isNotEmpty() && (! $needsPayment || $paymentGateways->isNotEmpty());
    $submitTitle = $needsPayment ? 'ادامه و پرداخت' : 'ثبت سفارش';
@endphp

<x-user-panel title="ثبت سفارش" sub-title="نشانی را انتخاب کن، روش پرداخت را ببین و سفارش را تمام کن.">
    <div class="store-cart store-checkout">
        @if (session('success'))
            <div class="store-cart-message" role="status">{{ session('success') }}</div>
        @endif

        @if (session('discount_error'))
            <div class="store-cart-message store-cart-message--error" role="alert">{{ session('discount_error') }}</div>
        @endif
        @error('discount_code')
            <div class="store-cart-message store-cart-message--error" role="alert">{{ $message }}</div>
        @enderror

        <ol class="store-cart-steps" aria-label="مراحل سفارش">
            <li class="is-done">
                <a href="{{ route('cart.index') }}"><span>۱</span> سبد خرید</a>
            </li>
            <li class="is-current"><span>۲</span> نشانی و ارسال</li>
            <li><span>۳</span> پرداخت</li>
        </ol>

        <div class="store-cart-layout">
            @if ($addresses->isNotEmpty())
                <x-card.form
                    id="store-checkout-form"
                    class="store-cart-form store-checkout-form"
                    :action="route('checkout.store')"
                >
                    <section class="store-checkout-section">
                        <div class="store-cart-board__head">
                            <div>
                                <h2>نشانی و ارسال</h2>
                                <p>سفارش به نشانی انتخاب‌شده می‌رود و هزینه ارسال همان‌جا حساب می‌شود.</p>
                            </div>
                            <a class="store-cart-board__shop" href="{{ route('account.addresses.create', ['return_to' => 'checkout']) }}">افزودن نشانی</a>
                        </div>

                        <div class="store-checkout-addresses" role="radiogroup" aria-label="نشانی تحویل">
                            @foreach ($addresses as $address)
                                <x-form.address-option
                                    :address="$address"
                                    :checked="(string) old('address_id', $addresses->first()->id) === (string) $address->id"
                                    :shipping-amount="$shippingQuotes[$address->id] ?? 0"
                                />
                            @endforeach
                        </div>
                        @error('address_id')
                            <p class="store-checkout-address-error" role="alert">{{ $message }}</p>
                        @enderror
                    </section>

                    @if ($needsPayment)
                        <section class="store-checkout-section">
                            <div class="store-cart-board__head">
                                <div>
                                    <h2>روش پرداخت</h2>
                                    <p>بعد از ثبت سفارش به درگاه امن منتقل می‌شوی.</p>
                                </div>
                            </div>

                            @if ($paymentGateways->isEmpty())
                                <p class="store-checkout-address-error" role="alert">درگاه پرداخت فعالی وجود ندارد. بعد از فعال‌شدن درگاه در پنل مدیریت دوباره تلاش کنید.</p>
                            @else
                                <div class="store-checkout-gateways" role="radiogroup" aria-label="روش پرداخت">
                                    @foreach ($paymentGateways as $gateway)
                                        <label class="store-checkout-gateway" for="checkout_gateway_{{ $gateway->id }}">
                                            <input
                                                id="checkout_gateway_{{ $gateway->id }}"
                                                class="store-checkout-gateway__radio"
                                                type="radio"
                                                name="payment_gateway_id"
                                                value="{{ $gateway->id }}"
                                                @checked($selectedGatewayId === $gateway->id)
                                                required
                                            >
                                            <span class="store-checkout-gateway__logo">
                                                <img src="{{ public_image_url($gateway->displayImage()) }}" alt="" width="72" height="72">
                                            </span>
                                            <span class="store-checkout-gateway__copy">
                                                <strong>{{ $gateway->name }}</strong>
                                                <small>پرداخت امن آنلاین · {{ $gateway->driver->trans() }}</small>
                                            </span>
                                            <span class="store-checkout-gateway__mark" aria-hidden="true">
                                                <i class="ki-filled ki-check"></i>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            @error('payment_gateway_id')
                                <p class="store-checkout-address-error" role="alert">{{ $message }}</p>
                            @enderror
                        </section>
                    @endif
                </x-card.form>
            @else
                <div class="store-cart-empty">
                    <span class="store-checkout-empty-icon" aria-hidden="true"><i class="ki-filled ki-geolocation"></i></span>
                    <h2>هنوز نشانی‌ای ثبت نکرده‌اید</h2>
                    <p>برای ثبت سفارش، اول یک نشانی تحویل به حساب خود اضافه کن. بعد همین‌جا هزینه ارسال را می‌بینی.</p>
                    <x-button href="{{ route('account.addresses.create', ['return_to' => 'checkout']) }}" icon="ki-filled ki-plus">افزودن نشانی</x-button>
                </div>
            @endif

            <aside
                class="store-cart-summary"
                aria-labelledby="store-checkout-summary-title"
                data-checkout-summary
                data-subtotal="{{ $cart->subtotal() }}"
                data-discount="{{ $discountQuote?->discountAmount ?? 0 }}"
            >
                <h2 id="store-checkout-summary-title">خلاصه سفارش</h2>
                <p class="store-cart-summary__note">{{ $lineCount }} کالا · {{ $unitCount }} عدد</p>

                <ul class="store-checkout-bag">
                    @foreach ($cart->items as $cartItem)
                        @php
                            $product = $cartItem->product;
                            $lineTotal = $product->price * $cartItem->count;
                        @endphp
                        <li>
                            <a href="{{ route('shop.products.show', ['productId' => $product->id]) }}">
                                <img src="{{ public_image_url($product->item->image) }}" alt="" loading="lazy">
                            </a>
                            <div>
                                <a href="{{ route('shop.products.show', ['productId' => $product->id]) }}">{{ $product->item->name }}</a>
                                <small>{{ $cartItem->count }} عدد · {{ number_format($product->price) }} تومان</small>
                            </div>
                            <strong>{{ number_format($lineTotal) }}</strong>
                        </li>
                    @endforeach
                </ul>

                @if ($discountQuote)
                    <div class="store-cart-coupon__applied">
                        <div>
                            <small>کد تخفیف اعمال شد</small>
                            <strong dir="ltr">{{ $discountQuote->discount->code }}</strong>
                            <em>−{{ number_format($discountQuote->discountAmount) }} تومان</em>
                        </div>
                    </div>
                @endif

                <dl>
                    <div><dt>قیمت کالاها ({{ $unitCount }})</dt><dd>{{ number_format($cart->subtotal()) }} تومان</dd></div>
                    @if ($discountQuote)
                        <div class="is-off"><dt>تخفیف کد</dt><dd>−{{ number_format($discountQuote->discountAmount) }} تومان</dd></div>
                    @endif
                    <div>
                        <dt>هزینه ارسال</dt>
                        <dd data-checkout-shipping>{{ $addresses->isEmpty() ? 'پس از نشانی' : ($selectedShipping > 0 ? number_format($selectedShipping) . ' تومان' : 'رایگان') }}</dd>
                    </div>
                    <div class="store-cart-summary__total">
                        <dt>مبلغ قابل پرداخت</dt>
                        <dd data-checkout-total>{{ $addresses->isEmpty() ? 'پس از نشانی' : number_format($payableAmount) . ' تومان' }}</dd>
                    </div>
                </dl>

                @if ($canPlace)
                    <x-button
                        type="submit"
                        class="store-cart-summary__checkout"
                        icon="ki-filled ki-wallet"
                        form="store-checkout-form"
                    >{{ $submitTitle }}</x-button>
                @endif
                <a class="store-cart-summary__continue" href="{{ route('cart.index') }}">بازگشت به سبد خرید</a>
                <p class="store-checkout-note">{{ $needsPayment ? 'پس از ثبت سفارش به درگاه پرداخت منتقل می‌شوی. موجودی کالا تا زمان پرداخت رزرو می‌شود.' : 'مبلغ این سفارش صفر است و بدون پرداخت ثبت می‌شود.' }}</p>
            </aside>
        </div>

        @if ($canPlace)
            <div class="store-cart-dock">
                <div>
                    <small>قابل پرداخت</small>
                    <strong data-checkout-total>{{ number_format($payableAmount) }} تومان</strong>
                </div>
                <button type="submit" form="store-checkout-form">{{ $submitTitle }}</button>
            </div>
        @endif
    </div>
    @if ($addresses->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.__storeCheckoutTotalsReady) return;
                window.__storeCheckoutTotalsReady = true;
                const summary = document.querySelector('[data-checkout-summary]');
                if (!summary) return;
                const radios = document.querySelectorAll('input[name="address_id"][data-shipping-amount]');
                const shippingLabels = document.querySelectorAll('[data-checkout-shipping]');
                const totalLabels = document.querySelectorAll('[data-checkout-total]');
                const subtotal = Number(summary.dataset.subtotal);
                const discount = Number(summary.dataset.discount || 0);
                const format = function (amount) {
                    return amount.toLocaleString('en-US') + ' تومان';
                };
                const update = function () {
                    const selected = Array.prototype.find.call(radios, function (radio) { return radio.checked; });
                    if (!selected) return;
                    const shipping = Number(selected.dataset.shippingAmount);
                    const total = Math.max(0, subtotal - discount) + shipping;
                    shippingLabels.forEach(function (label) {
                        label.textContent = shipping > 0 ? format(shipping) : 'رایگان';
                    });
                    totalLabels.forEach(function (label) {
                        label.textContent = format(total);
                    });
                };
                radios.forEach(function (radio) {
                    radio.addEventListener('change', update);
                });
                update();
            });
        </script>
    @endif
</x-user-panel>
