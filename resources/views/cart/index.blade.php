<x-user-panel title="سبد خرید" sub-title="کالاها را چک کن، تعداد را تنظیم کن و برو برای ثبت سفارش.">
    <div class="store-cart">
        @if (session('success'))
            <div class="store-cart-message" role="status">{{ session('success') }}</div>
        @endif

        @if (session('discount_error'))
            <div class="store-cart-message store-cart-message--error" role="alert">{{ session('discount_error') }}</div>
        @endif

        @if ($cart === null || $cart->items->isEmpty())
            <div class="store-cart-empty">
                <img src="{{ public_image_url('assets/media/cart/empty-cart.svg') }}" alt="" width="220" height="165">
                <h2>سبد خریدت هنوز خالی است</h2>
                <p>یک محصول قشنگ پیدا کن، به سبد اضافه کن و همین‌جا برای ثبت سفارش آماده‌اش کن.</p>
                <x-button href="{{ route('shop.index') }}" icon="ki-filled ki-shop">مشاهده فروشگاه</x-button>
            </div>
        @else
            @php
                $lineCount = $cart->items->count();
                $unitCount = (int) $cart->items->sum('count');
                $payable = $cart->subtotal() - ($discountQuote?->discountAmount ?? 0);
            @endphp

            <ol class="store-cart-steps" aria-label="مراحل سفارش">
                <li class="is-current"><span>۱</span> سبد خرید</li>
                <li><span>۲</span> نشانی و ارسال</li>
                <li><span>۳</span> پرداخت</li>
            </ol>

            <div class="store-cart-layout">
                <x-card.form
                    class="store-cart-form"
                    :action="route('cart.update')"
                    data-cart-form
                    :submit="['title' => 'ثبت تعداد', 'type' => 'light']"
                >
                    <button type="submit" class="sr-only" data-cart-update tabindex="-1">ثبت تعداد</button>
                    <div class="store-cart-board__head">
                        <div>
                            <h2>سبد خرید</h2>
                            <p>{{ $lineCount }} کالا · {{ $unitCount }} عدد</p>
                        </div>
                        <a class="store-cart-board__shop" href="{{ route('shop.index') }}">ادامه خرید</a>
                    </div>

                    <ul class="store-cart-list">
                        @foreach ($cart->items as $cartItem)
                            @php
                                $product = $cartItem->product;
                                $lineTotal = $product->price * $cartItem->count;
                                $swatch = $product->color?->value;
                                $swatchIsColor = is_string($swatch) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch) === 1;
                            @endphp
                            <li class="store-cart-item">
                                <div class="store-cart-item__media">
                                    <a href="{{ route('shop.products.show', ['productId' => $product->id]) }}">
                                        <img src="{{ public_image_url($product->item->image) }}" alt="" loading="lazy">
                                    </a>
                                    <div class="store-cart-qty">
                                        <button type="button" data-cart-qty="1" aria-label="افزایش تعداد {{ $product->item->name }}">+</button>
                                        <x-form.input
                                            :name="'items[' . $product->id . ']'"
                                            type="number"
                                            :value="$cartItem->count"
                                            min="1"
                                            :max="$product->stock"
                                            :aria-label="'تعداد ' . $product->item->name"
                                            required
                                        />
                                        <button type="button" data-cart-qty="-1" aria-label="کاهش تعداد {{ $product->item->name }}">−</button>
                                    </div>
                                </div>

                                <div class="store-cart-item__copy">
                                    <a class="store-cart-item__name" href="{{ route('shop.products.show', ['productId' => $product->id]) }}">{{ $product->item->name }}</a>
                                    <ul class="store-cart-item__meta">
                                        @if ($product->color)
                                            <li>
                                                <span @class(['store-cart-item__swatch', 'is-plain' => ! $swatchIsColor]) @if ($swatchIsColor) style="background: {{ $swatch }}" @endif></span>
                                                {{ $product->color->name }}
                                            </li>
                                        @endif
                                        @if ($product->size)
                                            <li>سایز {{ $product->size->value }}</li>
                                        @endif
                                        @if ($product->stock <= 5)
                                            <li class="is-stock">فقط {{ $product->stock }} عدد مانده</li>
                                        @else
                                            <li>ارسال بعد از ثبت نشانی</li>
                                        @endif
                                    </ul>
                                    <div class="store-cart-item__price">
                                        <strong>{{ number_format($lineTotal) }}</strong>
                                        <span>تومان</span>
                                        @if ($cartItem->count > 1)
                                            <small>{{ number_format($product->price) }} تومان برای هر عدد</small>
                                        @endif
                                    </div>
                                </div>

                                <x-button
                                    class="store-cart-item__remove"
                                    type="submit"
                                    variant="light"
                                    size="sm"
                                    :formaction="route('cart.items.remove')"
                                    name="product_id"
                                    :value="$product->id"
                                    aria-label="حذف {{ $product->item->name }} از سبد"
                                >حذف</x-button>
                            </li>
                        @endforeach
                    </ul>
                </x-card.form>

                <aside class="store-cart-summary" aria-labelledby="store-cart-summary-title">
                    <h2 id="store-cart-summary-title">خلاصه سفارش</h2>
                    <p class="store-cart-summary__note">هزینه ارسال بعد از انتخاب نشانی اضافه می‌شود.</p>

                    @if ($discountQuote)
                        <x-card.form
                            class="store-cart-coupon is-on"
                            :action="route('cart.discount.remove')"
                            method="DELETE"
                        >
                            <div class="store-cart-coupon__applied">
                                <div>
                                    <small>کد تخفیف اعمال شد</small>
                                    <strong dir="ltr">{{ $discountQuote->discount->code }}</strong>
                                    <em>−{{ number_format($discountQuote->discountAmount) }} تومان</em>
                                </div>
                                <button type="submit">حذف</button>
                            </div>
                        </x-card.form>
                    @else
                        <x-card.form
                            class="store-cart-coupon"
                            :action="route('cart.discount.apply')"
                        >
                            <label class="store-cart-coupon__label" for="input_discount_code">کد تخفیف</label>
                            <div class="store-cart-coupon__row">
                                <x-form.input
                                    id="input_discount_code"
                                    name="discount_code"
                                    :value="old('discount_code')"
                                    placeholder="کد را بنویس"
                                    dir="ltr"
                                    autocomplete="off"
                                    required
                                />
                                <button type="submit" class="store-cart-coupon__apply">اعمال</button>
                            </div>
                        </x-card.form>
                        @error('discount_code')
                            <p class="store-cart-summary__error" role="alert">{{ $message }}</p>
                        @enderror
                    @endif

                    <dl>
                        <div><dt>قیمت کالاها ({{ $unitCount }})</dt><dd>{{ number_format($cart->subtotal()) }} تومان</dd></div>
                        @if ($discountQuote)
                            <div class="is-off"><dt>تخفیف کد</dt><dd>−{{ number_format($discountQuote->discountAmount) }} تومان</dd></div>
                        @endif
                        <div><dt>هزینه ارسال</dt><dd>پس از نشانی</dd></div>
                        <div class="store-cart-summary__total"><dt>مبلغ نهایی کالاها</dt><dd>{{ number_format($payable) }} تومان</dd></div>
                    </dl>

                    <x-button href="{{ route('checkout.show') }}" class="store-cart-summary__checkout" icon="ki-filled ki-basket">تایید و تکمیل سفارش</x-button>
                    <a class="store-cart-summary__continue" href="{{ route('shop.index') }}">بازگشت به فروشگاه</a>
                </aside>
            </div>

            <div class="store-cart-dock">
                <div>
                    <small>قابل پرداخت</small>
                    <strong>{{ number_format($payable) }} تومان</strong>
                </div>
                <a href="{{ route('checkout.show') }}">ثبت سفارش</a>
            </div>
        @endif
    </div>
    @if ($cart !== null && $cart->items->isNotEmpty())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.__storeCartQtyReady) return;
                window.__storeCartQtyReady = true;
                const form = document.querySelector('[data-cart-form]');
                if (!form) return;
                const submitQuantities = function () {
                    const updater = form.querySelector('[data-cart-update]');
                    if (updater) {
                        updater.click();
                        return;
                    }
                    form.submit();
                };
                form.querySelectorAll('[data-cart-qty]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const field = button.closest('.store-cart-qty')?.querySelector('input[type="number"]');
                        if (!field) return;
                        const step = Number(button.getAttribute('data-cart-qty') || '0');
                        const min = Number(field.min || '1');
                        const max = Number(field.max || '999');
                        const next = Math.min(max, Math.max(min, Number(field.value || min) + step));
                        if (next === Number(field.value)) return;
                        field.value = String(next);
                        submitQuantities();
                    });
                });
                form.querySelectorAll('.store-cart-qty input[type="number"]').forEach(function (field) {
                    field.addEventListener('change', submitQuantities);
                });
            });
        </script>
    @endif
</x-user-panel>
