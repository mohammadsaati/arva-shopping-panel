@props([
    'address',
    'checked' => false,
    'name' => 'address_id',
    'shippingAmount' => null,
])

<label class="store-checkout-address" for="checkout_address_{{ $address->id }}">
    <input
        id="checkout_address_{{ $address->id }}"
        class="store-checkout-address__radio"
        type="radio"
        name="{{ $name }}"
        value="{{ $address->id }}"
        @if($shippingAmount !== null) data-shipping-amount="{{ $shippingAmount }}" @endif
        @checked($checked)
        required
    >
    <span class="store-checkout-address__mark" aria-hidden="true">
        <i class="ki-filled ki-check"></i>
    </span>
    <span class="store-checkout-address__content">
        <span class="store-checkout-address__title">
            <i class="ki-filled ki-geolocation" aria-hidden="true"></i>
            <strong>{{ $address->province->name }}، {{ $address->city->name }}</strong>
        </span>
        <span class="store-checkout-address__line">{{ $address->full_address }}</span>
        <span class="store-checkout-address__meta">
            <span>{{ $address->recipient_name ?: 'گیرنده ثبت نشده' }}</span>
            @if ($address->phone_number)
                <span dir="ltr">{{ $address->phone_number }}</span>
            @endif
        </span>
        @if ($shippingAmount !== null)
            <span @class(['store-checkout-address__ship', 'is-free' => $shippingAmount <= 0])>
                {{ $shippingAmount > 0 ? 'ارسال ' . number_format($shippingAmount) . ' تومان' : 'ارسال رایگان' }}
            </span>
        @endif
    </span>
</label>
