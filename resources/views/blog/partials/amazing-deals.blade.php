@php
    $dealItems = $amazingItems
        ->filter(fn ($item) => $item->products->first() !== null)
        ->values();
    $featuredItem = $dealItems->first();
    $featuredProduct = $featuredItem?->products->first();
    $moreDeals = $dealItems->slice(1);
    $featuredUnits = $featuredItem
        ? \App\Support\BlogDealCountdown::units($featuredItem->amazing_end_time)
        : null;
@endphp

@if ($featuredItem && $featuredProduct)
    <section class="store-blog-deals" id="deals" aria-labelledby="store-blog-deals-title">
        <img class="store-blog-deals__texture" src="{{ public_image_url('assets/media/blog/deals-texture.jpg') }}" alt="" aria-hidden="true">
        <img class="store-blog-deals__spark" src="{{ public_image_url('assets/media/blog/deals-spark.jpg') }}" alt="" aria-hidden="true">

        <header class="store-blog-deals__head">
            <div>
                <p>از ویترین مجله</p>
                <h2 id="store-blog-deals-title">پیشنهادهای شگفت‌انگیز</h2>
            </div>
            <span>منتخب سردبیر، فقط تا پایان زمان محدود</span>
        </header>

        <div class="store-blog-deals__board {{ $moreDeals->isEmpty() ? 'store-blog-deals__board--solo' : '' }}">
            <article class="store-blog-deals__hero">
                <a class="store-blog-deals__hero-media" href="{{ route('shop.products.show', ['productId' => $featuredProduct->id]) }}" aria-hidden="true" tabindex="-1">
                    <img src="{{ public_image_url($featuredItem->image ?: 'assets/media/blog/editorial-selection.png') }}" alt="" loading="lazy">
                </a>
                <div class="store-blog-deals__hero-copy">
                    <span class="store-blog-deals__kicker">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m12 3 1.8 4.8L19 9.5l-3.8 3.2L16.2 18 12 15.6 7.8 18l1-5.3L5 9.5l5.2-1.7z" /></svg>
                        پیشنهاد منتخب{{ $featuredItem->category ? ' · '.$featuredItem->category->name : '' }}
                    </span>
                    <h3>
                        <a href="{{ route('shop.products.show', ['productId' => $featuredProduct->id]) }}">{{ $featuredItem->name }}</a>
                    </h3>
                    @if ($featuredItem->description)
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($featuredItem->description), 110) }}</p>
                    @endif
                    <div class="store-blog-deals__price">
                        <strong>{{ number_format($featuredProduct->price) }}</strong>
                        <span>تومان</span>
                    </div>
                    @if ($featuredItem->amazing_end_time)
                        <div
                            class="store-blog-deals__clock"
                            data-amazing-countdown
                            data-amazing-end="{{ $featuredItem->amazing_end_time->getTimestamp() * 1000 }}"
                        >
                            <p class="store-blog-deals__clock-label">زمان باقی‌مانده</p>
                            <span class="store-blog-deals__sr-time" data-amazing-time role="timer" aria-live="off">{{ \App\Support\BlogDealCountdown::isExpired($featuredItem->amazing_end_time) ? 'پایان یافته' : 'در حال محاسبه…' }}</span>
                            <ol class="store-blog-deals__units" aria-hidden="true">
                                <li><strong data-deal-unit="days">{{ $featuredUnits['days'] }}</strong><span>روز</span></li>
                                <li><strong data-deal-unit="hours">{{ $featuredUnits['hours'] }}</strong><span>ساعت</span></li>
                                <li><strong data-deal-unit="minutes">{{ $featuredUnits['minutes'] }}</strong><span>دقیقه</span></li>
                                <li><strong data-deal-unit="seconds">{{ $featuredUnits['seconds'] }}</strong><span>ثانیه</span></li>
                            </ol>
                            <small>تا {{ jalali_date($featuredItem->amazing_end_time, 'Y/m/d H:i') }}</small>
                        </div>
                    @endif
                    <a class="store-blog-deals__cta" href="{{ route('shop.products.show', ['productId' => $featuredProduct->id]) }}">
                        مشاهده این پیشنهاد
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" /></svg>
                    </a>
                </div>
            </article>

            @if ($moreDeals->isNotEmpty())
                <div class="store-blog-deals__mosaic" role="list">
                    @foreach ($moreDeals as $item)
                        @php($product = $item->products->first())
                        <a class="store-blog-deals__tile" role="listitem" href="{{ route('shop.products.show', ['productId' => $product->id]) }}" wire:key="blog-deal-{{ $item->id }}">
                            <img src="{{ public_image_url($item->image ?: 'assets/media/blog/editorial-selection.png') }}" alt="" loading="lazy">
                            <span class="store-blog-deals__tile-copy">
                                @if ($item->category)<small>{{ $item->category->name }}</small>@endif
                                <strong>{{ $item->name }}</strong>
                                <em>{{ number_format($product->price) }} تومان</em>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif
