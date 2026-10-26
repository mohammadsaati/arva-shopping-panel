@props(['product' => null, 'item' => null, 'amazing' => false, 'showVariant' => false])

@php
    $item = $item ?? $product?->item;
    $product = $product ?? $item?->products?->first();
@endphp

@if ($item && $product)
<article class="store-product-card">
    <a class="store-product-card__image" href="{{ route('shop.products.show', ['productId' => $product->id]) }}" aria-label="مشاهده جزئیات {{ $item->name }}">
        <img
            src="{{ public_image_url($item->image) }}"
            alt="{{ $item->name }}"
            loading="lazy"
        >
        <span @class(['store-product-card__availability', 'is-unavailable' => $product->stock <= 0])>{{ $product->stock > 0 ? 'موجود' : 'ناموجود' }}</span>
        @if ($amazing)
            <span class="store-product-card__amazing-label">شگفت‌انگیز</span>
        @endif
    </a>

    <div class="store-product-card__body">
        @if ($item->category)
            <p class="store-product-card__category">{{ $item->category->name }}</p>
        @endif

        <h3 class="store-product-card__name"><a href="{{ route('shop.products.show', ['productId' => $product->id]) }}">{{ $item->name }}</a></h3>

        @if ($showVariant && ($product->color || $product->size))
            <p class="store-product-card__variant">
                @if ($product->color)
                    <span>رنگ: {{ $product->color->name }}</span>
                @endif
                @if ($product->size)
                    <span>سایز: {{ $product->size->value }}</span>
                @endif
            </p>
        @endif

        @if ($item->brand)
            <p class="store-product-card__brand">{{ $item->brand->name }}</p>
        @endif

        <div class="store-product-card__price-row">
            <strong>{{ number_format($product->price) }} <span>تومان</span></strong>
        </div>
        @if ($product->stock > 0)
            <x-card.form
                class="store-product-card__add-form"
                :action="route('cart.items.store', ['productId' => $product->id])"
                :submit="['title' => 'افزودن به سبد خرید', 'type' => 'primary']"
            />
        @endif
        @if ($amazing && $item->amazing_end_time)
            <div class="store-product-card__countdown" data-amazing-countdown data-amazing-end="{{ $item->amazing_end_time->getTimestamp() * 1000 }}">
                <span>زمان باقی‌مانده</span>
                <strong data-amazing-time role="timer" aria-live="off">در حال محاسبه…</strong>
                <small>تا {{ jalali_date($item->amazing_end_time, 'Y/m/d H:i') }}</small>
            </div>
        @endif
    </div>
</article>
@endif
