<header class="user-header">
    <div class="user-header__inner">
        <form class="user-search" role="search" method="GET" action="{{ route('shop.index') }}" data-item-search="{{ route('shop.suggest') }}" autocomplete="off">
            <button class="user-search__submit" type="submit" aria-label="جستجو">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <circle cx="10.8" cy="10.8" r="6.8" />
                    <path stroke-linecap="round" d="m16 16 4.3 4.3" />
                </svg>
            </button>
            <input
                type="search"
                name="search"
                value="{{ request()->query('search') }}"
                placeholder="جستجو در {{ site_name() }}..."
                aria-label="جستجو در محصولات"
                autocomplete="off"
            >
            <div class="user-search__panel" data-item-search-panel hidden></div>
        </form>

        <a class="user-brand" href="{{ route('home') }}" aria-label="{{ site_name() }} - صفحه اصلی">
            @if (site_settings()?->logo_path)
                <img src="{{ public_image_url(site_settings()->logo_path) }}" alt="{{ site_name() }}" class="user-brand__logo" />
            @else
                <span class="user-brand__accent">{{ site_name() }}</span>
            @endif
        </a>

        <div class="user-header__actions">
            <a class="user-cart-link" href="{{ route('cart.index') }}" aria-label="سبد خرید، {{ $cartCount }} کالا">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4h2l2.1 10.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 8H6" />
                    <circle cx="10" cy="20" r="1" />
                    <circle cx="18" cy="20" r="1" />
                </svg>
                <span>سبد خرید</span>
                @if ($cartCount > 0)
                    <span class="user-cart-link__count">{{ $cartCount }}</span>
                @endif
            </a>

            @auth
                @if (Route::has('account.show'))
                    <a class="user-account-link" href="{{ route('account.show') }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <circle cx="12" cy="8" r="3.5" />
                            <path stroke-linecap="round" d="M5 20a7 7 0 0 1 14 0" />
                        </svg>
                        <span>حساب کاربری</span>
                    </a>
                @endif
            @else
                <a class="user-account-link" href="{{ route('login') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <circle cx="12" cy="8" r="3.5" />
                        <path stroke-linecap="round" d="M5 20a7 7 0 0 1 14 0" />
                    </svg>
                    <span>ورود / ثبت‌نام</span>
                </a>
            @endauth

            <button class="user-icon-button" type="button" data-theme-toggle aria-label="تغییر پوسته" aria-pressed="false">
                <svg class="dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" />
                </svg>
                <svg class="hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <circle cx="12" cy="12" r="4" />
                    <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
                </svg>
            </button>
        </div>
    </div>

    <nav class="user-primary-nav" aria-label="منوی اصلی">
        <div class="user-primary-nav__inner">
            <a href="{{ route('home') }}" @class(['is-active' => request()->routeIs('home') && ! request()->query('search')])>صفحه اصلی</a>

            <details class="user-primary-nav__dropdown" data-mega-menu>
                <summary class="user-primary-nav__trigger" aria-controls="user-category-mega-menu">
                    دسته‌بندی‌ها
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                    </svg>
                </summary>

                <div class="user-mega-menu" id="user-category-mega-menu">
                    <div class="user-mega-menu__heading">
                        <div>
                            <p>دسته‌بندی‌های {{ site_name() }}</p>
                            <span>روی یک دسته‌بندی بروید تا زیر دسته‌های آن را ببینید.</span>
                        </div>
                        <a href="{{ route('shop.index') }}">مشاهده همه محصولات</a>
                    </div>

                    @if ($navigationCategories->isNotEmpty())
                        <div class="user-mega-menu__content">
                            <ul class="user-mega-menu__parents" aria-label="دسته‌بندی‌های اصلی">
                                @foreach ($navigationCategories as $category)
                                    <li>
                                        <button
                                            class="user-mega-menu__parent-button"
                                            type="button"
                                            data-category-mega-trigger="mega-category-panel-{{ $category->id }}"
                                            aria-controls="mega-category-panel-{{ $category->id }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            @class(['is-active' => $loop->first])
                                        >
                                            <i class="user-category-icon {{ $category->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i>
                                            <span>{{ $category->name }}</span>
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
                                            </svg>
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="user-mega-menu__children">
                                @foreach ($navigationCategories as $category)
                                    <section
                                        class="user-mega-menu__children-panel"
                                        id="mega-category-panel-{{ $category->id }}"
                                        data-category-mega-panel="mega-category-panel-{{ $category->id }}"
                                        aria-label="زیر دسته‌های {{ $category->name }}"
                                        @if (! $loop->first) hidden @endif
                                    >
                                        <div class="user-mega-menu__children-heading">
                                            <h3>{{ $category->name }}</h3>
                                            <a href="{{ route('categories.show', ['slug' => $category->slug]) }}">مشاهده محصولات این دسته</a>
                                        </div>

                                        @if ($category->children->isNotEmpty())
                                            <ul>
                                                @foreach ($category->children as $childCategory)
                                                    <li>
                                                        <a href="{{ route('categories.show', ['slug' => $childCategory->slug]) }}">
                                                            <i class="user-category-icon {{ $childCategory->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i>
                                                            <span>{{ $childCategory->name }}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p>برای این دسته‌بندی، زیر دسته‌ای ثبت نشده است.</p>
                                        @endif
                                    </section>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="user-mega-menu__empty">در حال حاضر دسته‌بندی فعالی برای نمایش وجود ندارد.</p>
                    @endif
                </div>
            </details>

            <a href="{{ route('shop.index') }}" @class(['is-active' => request()->routeIs('shop.*', 'categories.*', 'sections.*')])>فروشگاه</a>
            <a href="{{ route('blog.index') }}" @class(['is-active' => request()->routeIs('blog.*')])>مجله {{ site_name() }}</a>
            <a href="{{ route('home') }}#about">درباره ما</a>
        </div>
    </nav>

</header>

<x-user.mobile-header :navigation-categories="$navigationCategories" :cart-count="$cartCount" />
