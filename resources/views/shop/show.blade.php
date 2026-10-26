@php
    $item = $product->item;
    $specificationRows = $item->specificationDisplayRows();
@endphp

<x-user-layout :title="$item->name . ' | ' . site_name()">
    <section class="store-detail container">
        <nav class="store-shop__breadcrumbs" aria-label="مسیر صفحه">
            <a href="{{ route('home') }}">صفحه اصلی</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('shop.index') }}">فروشگاه</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('categories.show', ['slug' => $item->category->slug]) }}">{{ $item->category->name }}</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $item->name }}</span>
        </nav>

        <div class="store-detail__layout">
            <article class="store-detail__product">
                <div class="store-detail__gallery" data-product-gallery>
                    <div class="store-detail__image">
                        <img data-gallery-main src="{{ public_image_url($product->images->first()?->image ?? $item->image) }}" alt="{{ $item->name }}">
                    </div>
                    @if ($product->images->count() > 1)
                        <div class="store-detail__thumbnails" aria-label="تصاویر محصول انتخابی">
                            @foreach ($product->images as $image)
                                <button
                                    type="button"
                                    class="store-detail__thumbnail"
                                    data-gallery-thumb
                                    data-image-src="{{ public_image_url($image->image) }}"
                                    data-image-alt="{{ $item->name }}، تصویر {{ $loop->iteration }}"
                                    aria-label="نمایش تصویر {{ $loop->iteration }}"
                                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                                >
                                    <img src="{{ public_image_url($image->image) }}" alt="" loading="lazy">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="store-detail__info">
                    <div class="store-detail__meta">
                        @if ($item->brand)
                            <span>{{ $item->brand->name }}</span>
                        @endif
                        <a href="{{ route('categories.show', ['slug' => $item->category->slug]) }}">{{ $item->category->name }}</a>
                    </div>

                    <h1>{{ $item->name }}</h1>
                    <p class="store-detail__code">کد کالا: {{ $item->code }}</p>

                    <section class="store-detail__variants" id="variants" aria-labelledby="store-detail-variants-title">
                        <div class="store-detail__section-heading">
                            <h2 id="store-detail-variants-title">مدل‌های این کالا</h2>
                            <span>{{ $item->products->count() }} مدل</span>
                        </div>

                        <div class="store-detail__variant-list">
                            @foreach ($item->products as $variant)
                                <a
                                    href="{{ route('shop.products.show', ['productId' => $variant->id]) }}"
                                    @class(['store-detail__variant', 'is-selected' => $variant->id === $product->id, 'is-unavailable' => $variant->stock <= 0])
                                    aria-current="{{ $variant->id === $product->id ? 'true' : 'false' }}"
                                >
                                    <span class="store-detail__variant-name">
                                        @if ($variant->color || $variant->size)
                                            @if ($variant->color)
                                                رنگ {{ $variant->color->name }}
                                            @endif
                                            @if ($variant->color && $variant->size)
                                                <span aria-hidden="true">/</span>
                                            @endif
                                            @if ($variant->size)
                                                سایز {{ $variant->size->value }}
                                            @endif
                                        @else
                                            مدل شماره {{ $variant->id }}
                                        @endif
                                    </span>
                                    <span class="store-detail__variant-price">{{ number_format($variant->price) }} تومان</span>
                                    <span class="store-detail__variant-stock">{{ $variant->stock > 0 ? 'موجود' : 'ناموجود' }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>

                    <div class="store-detail__features" aria-label="مشخصات کالا">
                        <div><span>دسته‌بندی</span><strong>{{ $item->category->name }}</strong></div>
                        @if ($item->brand)
                            <div><span>برند</span><strong>{{ $item->brand->name }}</strong></div>
                        @endif
                        @if ($product->color)
                            <div><span>رنگ انتخابی</span><strong>{{ $product->color->name }}</strong></div>
                        @endif
                        @if ($product->size)
                            <div><span>سایز انتخابی</span><strong>{{ $product->size->value }}</strong></div>
                        @endif
                        @foreach ($specificationRows->filter(fn (array $row) => $row['specification']->is_highlighted)->take(4) as $row)
                            <div><span>{{ $row['specification']->name }}</span><strong>{{ $row['value'] }}</strong></div>
                        @endforeach
                    </div>
                    @if ($specificationRows->isNotEmpty())
                        <a class="store-detail__specifications-link" href="#product-specifications" data-product-info-tab-link="specifications">مشاهده همه مشخصات <span aria-hidden="true">←</span></a>
                    @endif
                </div>
            </article>

            <aside class="store-detail__purchase" aria-label="قیمت و موجودی مدل انتخابی">
                <span class="store-detail__purchase-eyebrow">مدل انتخابی</span>
                <h2>{{ $item->name }}</h2>
                <span @class(['store-detail__stock', 'is-unavailable' => $product->stock <= 0])>
                    {{ $product->stock > 0 ? 'موجود در فروشگاه' : 'در حال حاضر ناموجود' }}
                </span>

                <div class="store-detail__purchase-price">
                    <strong>{{ number_format($product->price) }}</strong>
                    <span>تومان</span>
                </div>

                <dl>
                    @if ($product->color)
                        <div><dt>رنگ</dt><dd>{{ $product->color->name }}</dd></div>
                    @endif
                    @if ($product->size)
                        <div><dt>سایز</dt><dd>{{ $product->size->value }}</dd></div>
                    @endif
                    <div><dt>هزینه ارسال پیش‌فرض</dt><dd>{{ $product->shipping_price > 0 ? number_format($product->shipping_price) . ' تومان' : 'رایگان' }}</dd></div>
                </dl>
                <p class="store-detail__shipping-note">هزینه نهایی ارسال با انتخاب نشانی در مرحله ثبت سفارش مشخص می‌شود.</p>

                @if ($product->stock > 0)
                    <x-card.form
                        class="store-detail__cart-form"
                        :action="route('cart.items.store', ['productId' => $product->id])"
                        :submit="['title' => 'افزودن به سبد خرید', 'type' => 'primary']"
                    >
                        <x-form.input
                            name="quantity"
                            type="number"
                            label="تعداد"
                            value="1"
                            min="1"
                            :max="$product->stock"
                            required
                        />
                    </x-card.form>
                @else
                    <p class="store-detail__unavailable-note">این مدل فعلاً موجود نیست.</p>
                @endif

                @if ($item->products->count() > 1)
                    <a class="store-detail__choose-link" href="#variants">مشاهده مدل‌های دیگر</a>
                @endif
            </aside>
        </div>

        @if (filled($item->description) || $specificationRows->isNotEmpty())
            @php
                $hasDescription = filled($item->description);
                $hasSpecifications = $specificationRows->isNotEmpty();
                $hasBothInfoTabs = $hasDescription && $hasSpecifications;
                $initialInfoTab = $hasDescription ? 'description' : 'specifications';
            @endphp
            <section class="store-detail__information-card" id="product-specifications" data-product-info-tabs aria-labelledby="store-detail-information-title">
                <header class="store-detail__information-header">
                    <span class="store-detail__information-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 3.75h7l4.25 4.3v11.2A1.75 1.75 0 0 1 16.5 21h-9A1.75 1.75 0 0 1 5.75 19.25v-13.75A1.75 1.75 0 0 1 7.5 3.75Z" />
                            <path d="M14 4v5h5M8.5 13h7M8.5 16.5h7" />
                        </svg>
                    </span>
                    <div class="store-detail__information-title">
                        <h2 id="store-detail-information-title">{{ $hasBothInfoTabs ? 'اطلاعات کالا' : ($hasDescription ? 'معرفی محصول' : 'مشخصات کالا') }}</h2>
                        <span>جزئیات کامل این کالا</span>
                    </div>
                    @if ($hasSpecifications)
                        <span class="store-detail__information-count">{{ $specificationRows->count() }} مشخصه</span>
                    @endif
                </header>

                @if ($hasBothInfoTabs)
                    <div class="store-detail__information-tabs" role="tablist" aria-label="اطلاعات کالا">
                        <button
                            id="product-description-tab"
                            type="button"
                            role="tab"
                            aria-selected="{{ $initialInfoTab === 'description' ? 'true' : 'false' }}"
                            aria-controls="product-description-panel"
                            tabindex="{{ $initialInfoTab === 'description' ? '0' : '-1' }}"
                            data-product-info-tab="description"
                        >توضیحات</button>
                        <button
                            id="product-specifications-tab"
                            type="button"
                            role="tab"
                            aria-selected="{{ $initialInfoTab === 'specifications' ? 'true' : 'false' }}"
                            aria-controls="product-specifications-panel"
                            tabindex="{{ $initialInfoTab === 'specifications' ? '0' : '-1' }}"
                            data-product-info-tab="specifications"
                        >مشخصات کالا</button>
                    </div>
                @endif

                <div class="store-detail__information-body">
                    @if ($hasDescription)
                        <section
                            class="store-detail__information-panel store-detail__description"
                            @if ($hasBothInfoTabs) id="product-description-panel" role="tabpanel" aria-labelledby="product-description-tab" tabindex="0" data-product-info-panel="description" @endif
                            @if ($hasBothInfoTabs && $initialInfoTab !== 'description') hidden @endif
                        >
                            <p>{{ $item->description }}</p>
                        </section>
                    @endif

                    @if ($hasSpecifications)
                        <section
                            class="store-detail__information-panel store-detail__specifications"
                            @if ($hasBothInfoTabs) id="product-specifications-panel" role="tabpanel" aria-labelledby="product-specifications-tab" tabindex="0" data-product-info-panel="specifications" @endif
                            @if ($hasBothInfoTabs && $initialInfoTab !== 'specifications') hidden @endif
                        >
                            @foreach ($specificationRows->groupBy(fn (array $row) => $row['specification']->group->id) as $groupRows)
                                <section class="store-detail__specification-group" aria-labelledby="specification-group-{{ $loop->index }}">
                                    <h3 id="specification-group-{{ $loop->index }}">{{ $groupRows->first()['specification']->group->name }}</h3>
                                    <dl>
                                        @foreach ($groupRows as $row)
                                            <div><dt>{{ $row['specification']->name }}</dt><dd>{{ $row['value'] }}</dd></div>
                                        @endforeach
                                    </dl>
                                </section>
                            @endforeach
                        </section>
                    @endif
                </div>
            </section>
        @endif

        <livewire:storefront.product-reviews :item-id="$item->id" />

        @if ($relatedProducts->isNotEmpty())
            <section
                class="store-karin-section store-detail__related"
                data-carousel="related-products-{{ $product->id }}"
                aria-labelledby="store-detail-related-title"
            >
                <div class="store-karin-section__heading">
                    <div class="store-karin-section__identity">
                        <span class="store-karin-section__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3.5 14.6 9l6 .8-4.3 4.2 1 6-5.3-2.8-5.3 2.8 1-6-4.3-4.2 6-.8L12 3.5Z" />
                            </svg>
                        </span>
                        <div>
                            <h2 id="store-detail-related-title" class="store-karin-section__title">محصولات <span>مرتبط</span></h2>
                            <p class="store-karin-section__subtitle">محصولات دیگری از دسته‌بندی {{ $item->category->name }}</p>
                        </div>
                    </div>
                    <div class="store-detail__related-actions">
                        <a class="store-catalog__all-link" href="{{ route('categories.show', ['slug' => $item->category->slug]) }}">مشاهده همه</a>
                        <div class="store-karin-section__controls" aria-label="پیمایش محصولات مرتبط">
                            <button type="button" class="slider-navigate_btn" data-carousel-prev="related-products-{{ $product->id }}" aria-label="محصولات مرتبط قبلی">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 5 7 7-7 7" /></svg>
                            </button>
                            <button type="button" class="slider-navigate_btn" data-carousel-next="related-products-{{ $product->id }}" aria-label="محصولات مرتبط بعدی">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 5-7 7 7 7" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="store-karin-section__track" data-carousel-track>
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="store-karin-section__item">
                            <x-user.product-card :product="$relatedProduct" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </section>
</x-user-layout>
