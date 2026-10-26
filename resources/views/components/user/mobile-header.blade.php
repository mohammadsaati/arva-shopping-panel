@props(['navigationCategories', 'cartCount' => 0])

<div class="user-mobile-shell">
    <nav class="user-mobile-topbar" aria-label="منوی بالای موبایل">
        <button class="user-mobile-menu-toggle" type="button" data-mobile-menu-open aria-controls="user-mobile-menu" aria-expanded="false" aria-label="باز کردن منو">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <a class="user-mobile-topbar__brand" href="{{ route('home') }}" aria-label="{{ site_name() }} - صفحه اصلی">
            @if (site_settings()?->logo_path)<img src="{{ public_image_url(site_settings()->logo_path) }}" alt="{{ site_name() }}" class="user-mobile-brand-logo" />@else<span>{{ site_name() }}</span>@endif
        </a>

        <button class="user-mobile-menu-toggle" type="button" data-theme-toggle aria-label="تغییر پوسته" aria-pressed="false">
            <svg class="dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" />
            </svg>
            <svg class="hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <circle cx="12" cy="12" r="4" />
                <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
            </svg>
        </button>
    </nav>

    <button class="user-mobile-search-trigger" type="button" data-mobile-search-open aria-controls="user-mobile-search" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <circle cx="10.8" cy="10.8" r="6.8" />
            <path stroke-linecap="round" d="m16 16 4.3 4.3" />
        </svg>
        <span>جستجو در <strong>{{ site_name() }}</strong></span>
    </button>

    <div class="user-mobile-menu" id="user-mobile-menu" data-mobile-menu aria-hidden="true" inert>
        <button class="user-mobile-menu__backdrop" type="button" data-mobile-menu-close aria-label="بستن منو"></button>

        <aside class="user-mobile-menu__drawer" role="dialog" aria-modal="true" aria-label="منوی فروشگاه">
            <section class="user-mobile-menu__view is-active" data-mobile-menu-view="main" aria-hidden="false">
                <div class="user-mobile-menu__header">
                    <a class="user-mobile-menu__brand" href="{{ route('home') }}">
                        @if (site_settings()?->logo_path)<img src="{{ public_image_url(site_settings()->logo_path) }}" alt="{{ site_name() }}" class="user-mobile-brand-logo" />@else<span>{{ site_name() }}</span>@endif
                    </a>
                    <button class="user-mobile-menu__close" type="button" data-mobile-menu-close aria-label="بستن منو">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
                        </svg>
                    </button>
                </div>

                <nav class="user-mobile-menu__links" aria-label="پیوندهای اصلی">
                    <a href="{{ route('home') }}">
                        <i class="ki-filled ki-home-2" aria-hidden="true"></i>
                        <span>صفحه اصلی</span>
                    </a>
                    <button type="button" data-mobile-menu-view-open="categories" aria-controls="user-mobile-categories">
                        <i class="ki-filled ki-category" aria-hidden="true"></i>
                        <span>دسته‌بندی‌ها</span>
                        <svg class="user-mobile-menu__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                        </svg>
                    </button>
                    <a href="{{ route('shop.index') }}">
                        <i class="ki-filled ki-shop" aria-hidden="true"></i>
                        <span>فروشگاه</span>
                    </a>
                    <a href="{{ route('blog.index') }}">
                        <i class="ki-filled ki-book" aria-hidden="true"></i>
                        <span>مجله {{ site_name() }}</span>
                    </a>
                    <a href="{{ route('cart.index') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6" />
                            <circle cx="10" cy="20" r="1" />
                            <circle cx="18" cy="20" r="1" />
                        </svg>
                        <span>سبد خرید @if ($cartCount > 0) ({{ $cartCount }}) @endif</span>
                    </a>
                    <a href="{{ route('home') }}#about">
                        <i class="ki-filled ki-information-2" aria-hidden="true"></i>
                        <span>درباره ما</span>
                    </a>
                    @auth
                        @if (Route::has('account.show'))
                            <a href="{{ route('account.show') }}">
                                <i class="ki-filled ki-profile-circle" aria-hidden="true"></i>
                                <span>حساب کاربری</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}">
                            <i class="ki-filled ki-profile-circle" aria-hidden="true"></i>
                            <span>ورود / ثبت‌نام</span>
                        </a>
                    @endauth
                </nav>
            </section>

            <section
                class="user-mobile-menu__view"
                id="user-mobile-categories"
                data-mobile-menu-view="categories"
                aria-hidden="true"
                inert
            >
                <div class="user-mobile-menu__view-header">
                    <button type="button" data-mobile-menu-back="main" aria-label="بازگشت به منوی اصلی">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                        </svg>
                        <span>دسته‌بندی‌ها</span>
                    </button>
                    <a href="{{ route('shop.index') }}">همه محصولات</a>
                </div>

                @if ($navigationCategories->isNotEmpty())
                    <ul class="user-mobile-category-list" aria-label="دسته‌بندی‌های اصلی">
                        @foreach ($navigationCategories as $category)
                            <li>
                                <button
                                    class="user-mobile-category-card"
                                    type="button"
                                    data-mobile-menu-view-open="category-{{ $category->id }}"
                                    aria-controls="user-mobile-category-{{ $category->id }}"
                                >
                                    @if ($category->image)
                                        <img class="user-mobile-category-card__image" src="{{ public_image_url($category->image) }}" alt="" loading="lazy">
                                    @endif
                                    <span class="user-mobile-category-card__content">
                                        <span class="user-mobile-category-card__icon">
                                            <i class="{{ $category->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i>
                                        </span>
                                        <span class="user-mobile-category-card__name">{{ $category->name }}</span>
                                        <svg class="user-mobile-menu__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                        </svg>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="user-mobile-menu__empty">در حال حاضر دسته‌بندی فعالی برای نمایش وجود ندارد.</p>
                @endif
            </section>

            @foreach ($navigationCategories as $category)
                <section
                    class="user-mobile-menu__view"
                    id="user-mobile-category-{{ $category->id }}"
                    data-mobile-menu-view="category-{{ $category->id }}"
                    aria-hidden="true"
                    inert
                >
                    <div class="user-mobile-menu__view-header">
                        <button type="button" data-mobile-menu-back="categories" aria-label="بازگشت به دسته‌بندی‌ها">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
                            </svg>
                            <span>دسته‌بندی‌ها</span>
                        </button>
                    </div>

                    <div class="user-mobile-category-detail__heading">
                        <span class="user-mobile-category-card__icon">
                            <i class="{{ $category->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i>
                        </span>
                        <h2>{{ $category->name }}</h2>
                    </div>

                    <a class="user-mobile-category-detail__all" href="{{ route('categories.show', ['slug' => $category->slug]) }}">مشاهده محصولات این دسته</a>

                    @if ($category->children->isNotEmpty())
                        <ul class="user-mobile-category-children" aria-label="زیر دسته‌های {{ $category->name }}">
                            @foreach ($category->children as $childCategory)
                                <li>
                                    <a href="{{ route('categories.show', ['slug' => $childCategory->slug]) }}">
                                        <i class="user-category-icon {{ $childCategory->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i>
                                        <span>{{ $childCategory->name }}</span>
                                        <svg class="user-mobile-menu__chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                        </svg>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="user-mobile-menu__empty">برای این دسته‌بندی، زیر دسته‌ای ثبت نشده است.</p>
                    @endif
                </section>
            @endforeach
        </aside>
    </div>

    <div class="user-mobile-search" id="user-mobile-search" data-mobile-search aria-hidden="true" inert>
        <div class="user-mobile-search__header">
            <form class="user-search" role="search" method="GET" action="{{ route('shop.index') }}" data-item-search="{{ route('shop.suggest') }}" autocomplete="off">
                <button type="submit" aria-label="جستجو">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <circle cx="10.8" cy="10.8" r="6.8" />
                        <path stroke-linecap="round" d="m16 16 4.3 4.3" />
                    </svg>
                </button>
                <input type="search" name="search" value="{{ request()->query('search') }}" placeholder="جستجو در همه کالاها" aria-label="جستجو در محصولات" data-mobile-search-input autocomplete="off">
            </form>
            <button type="button" data-mobile-search-close aria-label="بستن جستجو">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
                </svg>
            </button>
        </div>
        <div class="user-search__panel" data-item-search-panel hidden></div>
        <p data-item-search-hint>نام کالا را بنویسید؛ بعد از دو حرف، پیشنهادها می‌آیند.</p>
    </div>

    <nav class="user-mobile-bottom-nav" aria-label="دسترسی سریع موبایل">
        <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home') && ! request()->query('search')])>
            <i class="ki-filled ki-home-2" aria-hidden="true"></i>
            <span>خانه</span>
        </a>
        <a href="{{ route('shop.index') }}" @class(['is-active' => request()->routeIs('shop.*', 'categories.*', 'sections.*')])>
            <i class="ki-filled ki-shop" aria-hidden="true"></i>
            <span>فروشگاه</span>
        </a>
        <button type="button" data-mobile-menu-open-view="categories" aria-controls="user-mobile-menu" aria-expanded="false">
            <i class="ki-filled ki-category" aria-hidden="true"></i>
            <span>دسته‌بندی</span>
        </button>
        <a href="{{ route('cart.index') }}" @class(['is-active' => request()->routeIs('cart.*', 'checkout.*')])>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6" />
                <circle cx="10" cy="20" r="1" />
                <circle cx="18" cy="20" r="1" />
            </svg>
            <span>سبد @if ($cartCount > 0) ({{ $cartCount }}) @endif</span>
        </a>
        @auth
            <a href="{{ route('account.show') }}" @class(['is-active' => request()->routeIs('account.*')])>
                <i class="ki-filled ki-profile-circle" aria-hidden="true"></i>
                <span>حساب من</span>
            </a>
        @else
            <a href="{{ route('login') }}" @class(['is-active' => request()->routeIs('login', 'register')])>
                <i class="ki-filled ki-profile-circle" aria-hidden="true"></i>
                <span>ورود</span>
            </a>
        @endauth
    </nav>
</div>
