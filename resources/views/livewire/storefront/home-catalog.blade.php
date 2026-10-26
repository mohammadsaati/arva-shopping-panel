<div class="store-home-catalog">
    @if (trim($search) === '')
        @if ($sliders->isNotEmpty())
            <section class="store-hero-carousel" aria-label="پیشنهادهای ویژه فروشگاه" data-hero-slider tabindex="0">
                <div class="store-hero-carousel__sky" aria-hidden="true">
                    <span></span><span></span>
                </div>
                <div class="store-hero-carousel__frame">
                    <div class="store-hero-carousel__viewport">
                        <div class="store-hero-carousel__track" data-slider-track>
                            @foreach ($sliders as $slider)
                                @php
                                    $slideUrl = $slider->homeUrl();
                                @endphp
                                <article class="store-hero-carousel__slide" data-slide wire:key="home-slider-{{ $slider->id }}">
                                    @if ($slideUrl)
                                        <a class="store-hero-carousel__cover" href="{{ $slideUrl }}" aria-label="{{ $slider->title }}">
                                            <img src="{{ public_image_url($slider->image) }}" alt="" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                            <span class="store-hero-carousel__shade"></span>
                                            <span class="store-hero-carousel__content">
                                                @if ($loop->first)
                                                    <h1 id="storefront-title" dir="auto">{{ $slider->title }}</h1>
                                                @else
                                                    <h2 dir="auto">{{ $slider->title }}</h2>
                                                @endif
                                                <em>مشاهده پیشنهاد</em>
                                            </span>
                                        </a>
                                    @else
                                        <img src="{{ public_image_url($slider->image) }}" alt="" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                        <span class="store-hero-carousel__shade"></span>
                                        <div class="store-hero-carousel__content">
                                            @if ($loop->first)
                                                <h1 id="storefront-title" dir="auto">{{ $slider->title }}</h1>
                                            @else
                                                <h2 dir="auto">{{ $slider->title }}</h2>
                                            @endif
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </div>
                    @if ($sliders->count() > 1)
                        <button type="button" class="store-hero-carousel__arrow store-hero-carousel__arrow--prev" data-slide-prev aria-label="اسلاید قبلی">
                            <i class="ki-filled ki-arrow-right" aria-hidden="true"></i>
                        </button>
                        <button type="button" class="store-hero-carousel__arrow store-hero-carousel__arrow--next" data-slide-next aria-label="اسلاید بعدی">
                            <i class="ki-filled ki-arrow-left" aria-hidden="true"></i>
                        </button>
                        <div class="store-hero-carousel__nav" aria-label="انتخاب اسلاید">
                            @foreach ($sliders as $slider)
                                <button type="button" data-slide-dot data-index="{{ $loop->index }}" aria-label="اسلاید {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>
        @else
            <section class="store-home__hero" aria-labelledby="storefront-title">
                <div class="store-home__hero-content">
                    <p class="store-home__eyebrow">فروشگاه اینترنتی {{ site_name() }}</p>
                    <h1 id="storefront-title" class="store-home__title">برای انتخاب‌های خوب، از همین‌جا شروع کنید.</h1>
                    <p class="store-home__description">محصولات منتخب را با تجربه‌ای ساده، سریع و مطمئن پیدا کنید.</p>
                    <div class="store-home__actions">
                        <a class="user-primary-link" href="{{ route('shop.index') }}">مشاهده همه محصولات</a>
                        <a class="store-home__secondary-link" href="#categories">دسته‌بندی‌ها</a>
                    </div>
                </div>
                <div class="store-home__visual" aria-hidden="true">
                    <div class="store-home__visual-orbit"></div>
                    <div class="store-home__visual-bag">
                        <svg viewBox="0 0 96 96" fill="none">
                            <path d="M20 34h56l5 46H15l5-46Z" fill="currentColor" opacity=".12" />
                            <path d="M32 38V27a16 16 0 0 1 32 0v11M20 38h56l5 42H15l5-42Z" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M37 55h22" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                        </svg>
                        <span>{{ site_name() }}</span>
                    </div>
                    <div class="store-home__visual-tag">انتخابی برای شما</div>
                </div>
            </section>
        @endif

        <section class="store-home-assurance" aria-label="اطمینان خرید از {{ site_name() }}">
            <ul>
                <li>
                    <span aria-hidden="true"><i class="ki-filled ki-verify"></i></span>
                    <div>
                        <strong>کالای اصل</strong>
                        <p>همان چیزی که می‌بینید را می‌فرستیم</p>
                    </div>
                </li>
                <li>
                    <span aria-hidden="true"><i class="ki-filled ki-shield-tick"></i></span>
                    <div>
                        <strong>پرداخت امن</strong>
                        <p>تا آخر مسیر خرید همراهتان هستیم</p>
                    </div>
                </li>
                <li>
                    <span aria-hidden="true"><i class="ki-filled ki-delivery"></i></span>
                    <div>
                        <strong>بسته‌بندی مرتب</strong>
                        <p>سالم و مرتب به دستتان می‌رسد</p>
                    </div>
                </li>
                <li>
                    <span aria-hidden="true"><i class="ki-filled ki-message-text"></i></span>
                    <div>
                        <strong>پاسخ‌گویی</strong>
                        <p>اگر سوالی بود، همین‌جا کمکتان می‌کنیم</p>
                    </div>
                </li>
            </ul>
        </section>
    @endif

    @if ($categories->isNotEmpty())
        <section id="categories" class="store-section" aria-labelledby="home-categories-title">
            <div class="store-section__heading">
                <div>
                    <p class="store-section__eyebrow">انتخاب سریع</p>
                    <h2 id="home-categories-title" class="store-section__title">دسته‌بندی‌های فروشگاه</h2>
                </div>
            </div>
            <div class="store-category-grid">
                @foreach ($categories as $category)
                    <a class="store-category-card" href="{{ route('categories.show', ['slug' => $category->slug]) }}" aria-label="مشاهده محصولات دسته {{ $category->name }}">
                        <div class="store-category-card__image"><img src="{{ public_image_url($category->image) }}" alt="{{ $category->name }}" loading="lazy"></div>
                        <h3>{{ $category->name }}</h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if ($amazingItems->isNotEmpty())
        @php
            $nextAmazingItem = $amazingItems->first();
        @endphp
        <section id="amazing" class="store-section store-amazing" data-carousel="home-amazing" aria-labelledby="home-amazing-title">
            <div class="store-amazing__layout">
                <aside class="store-amazing__feature">
                    <span class="store-amazing__icon" aria-hidden="true">
                        <svg viewBox="0 0 64 64" fill="none">
                            <path d="M15 46 45 16M20 20h.1M44 44h.1" stroke="currentColor" stroke-width="6" stroke-linecap="round" />
                            <circle cx="20" cy="20" r="8" stroke="currentColor" stroke-width="4" />
                            <circle cx="44" cy="44" r="8" stroke="currentColor" stroke-width="4" />
                        </svg>
                    </span>
                    <div class="store-amazing__feature-copy">
                        <p>فرصت‌های محدود</p>
                        <h2 id="home-amazing-title">شگفت‌انگیز</h2>
                        <span>تا پایان نزدیک‌ترین پیشنهاد</span>
                    </div>
                    @if ($nextAmazingItem?->amazing_end_time)
                        <div class="store-amazing__countdown" data-amazing-countdown data-amazing-end="{{ $nextAmazingItem->amazing_end_time->getTimestamp() * 1000 }}">
                            <strong data-amazing-time role="timer" aria-live="off">در حال محاسبه…</strong>
                        </div>
                    @endif
                    <a class="store-amazing__all-link" href="{{ route('shop.index') }}">مشاهده همه محصولات <span aria-hidden="true">←</span></a>
                </aside>

                <div class="store-amazing__products">
                    <button type="button" class="store-amazing__arrow store-amazing__arrow--prev" data-carousel-prev="home-amazing" aria-label="پیشنهادهای قبلی">›</button>
                    <div class="store-amazing__track" data-carousel-track role="list" aria-label="محصولات شگفت‌انگیز">
                        @foreach ($amazingItems as $item)
                            @if ($item->products->first())
                                <div class="store-amazing__item" role="listitem" wire:key="amazing-item-{{ $item->id }}">
                                    <x-user.product-card :item="$item" amazing />
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <button type="button" class="store-amazing__arrow store-amazing__arrow--next" data-carousel-next="home-amazing" aria-label="پیشنهادهای بعدی">‹</button>
                </div>
            </div>
        </section>
    @endif

    @if ($banners->isNotEmpty())
        <section class="store-section" aria-label="بنرهای فروشگاه">
            <div class="store-banner-grid">
                @foreach ($banners as $banner)
                    @if ($banner->homeUrl())
                        <a href="{{ $banner->homeUrl() }}" class="store-banner" aria-label="{{ $banner->product?->item?->name ?? $banner->category?->name ?? 'مشاهده بنر فروشگاه' }}">
                            <img src="{{ public_image_url($banner->image) }}" alt="" loading="lazy">
                        </a>
                    @else
                        <div class="store-banner"><img src="{{ public_image_url($banner->image) }}" alt="بنر فروشگاه" loading="lazy"></div>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    @foreach ($sections as $section)
        @php
            $sectionPicks = $section->items->filter(fn ($item) => $item->products->first());
            $sectionCover = $section->bg_image;
            $sectionStyle = '--store-karin-section-bg: '.($section->bg_color ?? '#eff6ff');
            if (filled($sectionCover)) {
                $sectionStyle .= '; --store-home-section-image: url(\''.e(public_image_url($sectionCover)).'\')';
            }
        @endphp
        <section
            id="section-{{ $section->id }}"
            data-carousel="section-{{ $section->id }}"
            @class(['store-section store-karin-section store-home-section', 'is-pictured' => filled($sectionCover)])
            aria-labelledby="section-title-{{ $section->id }}"
            style="{{ $sectionStyle }}"
        >
            <div class="store-home-section__sky" aria-hidden="true"><span></span><span></span></div>

            <div class="store-karin-section__heading store-home-section__heading">
                <div class="store-karin-section__identity">
                    <span class="store-home-section__index" aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                    <span class="store-karin-section__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 9h16l-1.2 11H5.2L4 9Z" />
                            <path d="M8 10V6a4 4 0 0 1 8 0v4" />
                            <path d="M9 14h6" />
                        </svg>
                    </span>
                    <div>
                        <p class="store-section__eyebrow">منتخب {{ site_name() }}</p>
                        <h2 id="section-title-{{ $section->id }}" class="store-karin-section__title" dir="auto">{{ $section->name }}</h2>
                        <p class="store-karin-section__subtitle">
                            {{ $sectionPicks->count() }} کالای آماده انتخاب
                        </p>
                    </div>
                </div>
                <div class="store-home-section__actions">
                    <a class="store-home-section__link" href="{{ route('sections.show', ['slug' => $section->slug]) }}">
                        <span class="store-home-section__link-full">بزن بریم این بخش را ببین</span>
                        <span class="store-home-section__link-short">بزن بریم</span>
                        <i class="ki-filled ki-arrow-left" aria-hidden="true"></i>
                    </a>
                    @if ($sectionPicks->count() > 1)
                        <div class="store-karin-section__controls" aria-label="پیمایش محصولات {{ $section->name }}">
                            <button type="button" class="slider-navigate_btn" data-carousel-prev="section-{{ $section->id }}" aria-label="محصولات قبلی">
                                <i class="ki-filled ki-arrow-right" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="slider-navigate_btn" data-carousel-next="section-{{ $section->id }}" aria-label="محصولات بعدی">
                                <i class="ki-filled ki-arrow-left" aria-hidden="true"></i>
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            @if ($sectionPicks->isNotEmpty())
                <div class="store-karin-section__track" data-carousel-track>
                    @foreach ($sectionPicks as $item)
                        <div class="store-karin-section__item" wire:key="section-{{ $section->id }}-item-{{ $item->id }}">
                            <x-user.product-card :product="$item->products->first()" />
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        @if ($section->images->isNotEmpty())
            <section class="store-karin-section__gallery store-home-section__gallery" aria-label="تصاویر بخش {{ $section->name }}">
                <div @class([
                    'store-karin-section__gallery-grid',
                    'is-single' => $section->images->count() === 1,
                    'is-pair' => $section->images->count() === 2,
                ])>
                    @foreach ($section->images as $sectionImage)
                        <a
                            class="store-karin-section__gallery-image"
                            href="{{ route('sections.show', ['slug' => $section->slug]) }}"
                            aria-label="مشاهده بخش {{ $section->name }}، تصویر {{ $loop->iteration }}"
                            wire:key="section-{{ $section->id }}-image-{{ $sectionImage->id }}"
                        >
                            <img
                                src="{{ public_image_url($sectionImage->image) }}"
                                alt="{{ $section->name }}، تصویر {{ $loop->iteration }}"
                                loading="lazy"
                            >
                            <span>یه نگاه به این بخش</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if (trim($search) === '')
        <livewire:storefront.home-blog-posts />
    @endif
</div>
