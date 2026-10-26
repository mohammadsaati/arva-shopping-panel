<div class="store-blog">
    <div class="store-blog__container">
        @include('blog.partials.navigation', ['categories' => $categories, 'activeCategory' => $selectedCategory, 'searchWire' => true])

        <header class="store-blog__intro" id="top">
            <div>
                <span class="store-blog__intro-label">مجله {{ site_name() }}</span>
                <h1>{{ $isSearching ? 'نتایج جستجو' : ($selectedCategory ? $selectedCategory->name : 'ایده‌های تازه برای انتخاب بهتر') }}</h1>
                <p class="store-blog__intro-meta">{{ $publishedCount }} نوشته منتشرشده{{ $categories->isNotEmpty() ? ' · '.$categories->count().' موضوع' : '' }}</p>
            </div>
            <p>{{ $isSearching ? 'نوشته‌های مرتبط با «'.$search.'» را ببین.' : ($selectedCategory?->description ?: 'راهنمای خرید، بررسی محصول و نکته‌های ساده‌ای که انتخاب را برایت آسان‌تر می‌کنند.') }}</p>
        </header>

        @if ($slides->isNotEmpty())
            <div class="store-blog__feature-grid">
                <section class="store-blog__slider" data-blog-slider aria-label="مطالب شاخص مجله" aria-roledescription="اسلایدر">
                    @foreach ($slides as $post)
                        <article class="store-blog__slide store-blog__slide--{{ ['violet', 'blue', 'copper'][$loop->index % 3] }}" data-blog-slide aria-label="{{ $loop->iteration }} از {{ $slides->count() }}" aria-roledescription="اسلاید" @if (! $loop->first) hidden @endif>
                            <div class="store-blog__slide-copy">
                                <span class="store-blog__slide-kicker">{{ $post->category->name }} · {{ $post->readingMinutes() }} دقیقه</span>
                                <h2>{{ $post->title }}</h2>
                                @if ($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
                                <a class="store-blog__slide-link" href="{{ route('blog.posts.show', $post->slug) }}">مطالعه نوشته <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" /></svg></a>
                            </div>
                            <div class="store-blog__slide-visual">
                                <img src="{{ public_image_url($post->featured_image ?: ($post->category->image ?: 'assets/media/blog/editorial-selection.png')) }}" alt="{{ $post->title }}" @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif>
                                <span aria-hidden="true">{{ sprintf('%02d', $loop->iteration) }}</span>
                            </div>
                        </article>
                    @endforeach
                    <div class="store-blog__slider-footer">
                        <div class="store-blog__slider-progress">
                            <span class="store-blog__slider-count" data-blog-slider-count aria-live="polite">01 / {{ sprintf('%02d', $slides->count()) }}</span>
                            <div class="store-blog__slider-dots" aria-label="انتخاب اسلاید">
                                @foreach ($slides as $post)
                                    <button class="store-blog__slider-dot" type="button" data-blog-slider-dot aria-label="نمایش اسلاید {{ $loop->iteration }}" aria-current="{{ $loop->first ? 'true' : 'false' }}"></button>
                                @endforeach
                            </div>
                        </div>
                        @if ($slides->count() > 1)
                            <div class="store-blog__slider-controls">
                                <button class="store-blog__slider-control" type="button" data-blog-slider-toggle aria-label="توقف پخش خودکار اسلایدها" aria-pressed="false"><svg class="store-blog__pause-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 5h3v14H7zm7 0h3v14h-3z" /></svg><svg class="store-blog__play-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m8 5 11 7-11 7z" /></svg></button>
                                <button class="store-blog__slider-control" type="button" data-blog-slider-prev aria-label="اسلاید قبلی"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18 9 12l6-6" /></svg></button>
                                <button class="store-blog__slider-control" type="button" data-blog-slider-next aria-label="اسلاید بعدی"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg></button>
                            </div>
                        @endif
                    </div>
                </section>
                <aside class="store-blog__feature-aside {{ $articles->count() > 1 ? '' : 'store-blog__feature-aside--single' }}" aria-label="پیشنهادهای مجله">
                    @if ($articles->count() > 1)
                        @php($asidePost = $articles->get(1))
                        <a class="store-blog__feature-story" href="{{ route('blog.posts.show', $asidePost->slug) }}">
                            <img src="{{ public_image_url($asidePost->featured_image ?: ($asidePost->category->image ?: 'assets/media/blog/editorial-selection.png')) }}" alt="" loading="lazy">
                            <small>{{ $asidePost->category->name }}</small>
                            <strong>{{ $asidePost->title }}</strong>
                            <span>مطالعه نوشته ←</span>
                        </a>
                    @endif
                    <a class="store-blog__feature-note" href="#latest">
                        <small>تازه‌های مجله</small>
                        <strong>نوشته‌های تازه برای یک انتخاب آگاهانه‌تر</strong>
                        <span>مرور نوشته‌ها ←</span>
                    </a>
                </aside>
            </div>
        @endif

        @if ($categories->isNotEmpty())
            <nav class="store-blog__topics" aria-label="دسته‌بندی‌های مجله">
                <span class="store-blog__topics-label">دنبال چه موضوعی هستید؟</span>
                <a class="store-blog__topic" href="{{ route('blog.index') }}#latest" @if (! $selectedCategory) aria-current="page" @endif><i class="ki-filled ki-category" aria-hidden="true"></i> همه مطالب</a>
                @foreach ($categories as $category)
                    <a class="store-blog__topic" href="{{ route('blog.categories.show', $category->slug) }}"><i class="ki-filled ki-book" aria-hidden="true"></i> {{ $category->name }}</a>
                @endforeach
            </nav>
        @endif

        @if ($banners->isNotEmpty())
            <section class="store-blog-spot {{ $banners->count() === 1 ? 'store-blog-spot--single' : '' }}" aria-label="پیشنهادهای مجله">
                @foreach ($banners as $banner)
                    <a class="store-blog-spot__card" href="{{ $banner->target_url ?: '#latest' }}" aria-label="{{ $banner->title }}">
                        <picture>
                            @if ($banner->mobile_image)<source media="(max-width: 767px)" srcset="{{ public_image_url($banner->mobile_image) }}">@endif
                            <img src="{{ public_image_url($banner->image) }}" alt="" loading="lazy">
                        </picture>
                        <span class="store-blog-spot__copy">
                            @if ($banner->eyebrow)<small>{{ $banner->eyebrow }}</small>@endif
                            <strong>{{ $banner->title }}</strong>
                            @if ($banner->description)<em>{{ $banner->description }}</em>@endif
                            <span class="store-blog-spot__cta">
                                {{ $banner->target_label ?: 'بیشتر بخوانید' }}
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" /></svg>
                            </span>
                        </span>
                    </a>
                @endforeach
            </section>
        @endif

        @if ($amazingItems->isNotEmpty())
            @include('blog.partials.amazing-deals', ['amazingItems' => $amazingItems])
        @endif

        <section class="store-blog__section" id="latest" aria-labelledby="store-blog-latest-title">
            <div class="store-blog__section-head">
                <div>
                    <p>{{ $isSearching ? 'جستجوی مجله' : 'برای شروع خواندن' }}</p>
                    <h2 id="store-blog-latest-title">{{ $isSearching ? 'نتایج «'.$search.'»' : ($selectedCategory ? 'مطالب '.$selectedCategory->name : 'تازه‌ترین مطالب مجله') }}</h2>
                </div>
                <span>{{ $isSearching ? $articles->count().' نتیجه' : 'ایده‌هایی برای خرید بهتر و مطمئن‌تر' }}</span>
            </div>
            @if ($articles->isNotEmpty())
                <div class="store-blog__content-grid">
                    <div class="store-blog__articles">
                        @foreach ($articles as $post)
                            <article class="store-blog__article" id="post-{{ $post->id }}">
                                <a class="store-blog__article-image" href="{{ route('blog.posts.show', $post->slug) }}" aria-label="مطالعه {{ $post->title }}">
                                    <img src="{{ public_image_url($post->featured_image ?: ($post->category->image ?: 'assets/media/blog/editorial-selection.png')) }}" alt="{{ $post->title }}" loading="lazy">
                                    <span class="store-blog__article-tag">{{ $post->category->name }}</span>
                                </a>
                                <div class="store-blog__article-body">
                                    <span class="store-blog__article-date">{{ jalali_date($post->published_at) }} · {{ $post->readingMinutes() }} دقیقه مطالعه</span>
                                    <h3><a href="{{ route('blog.posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
                                    @if ($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
                                    <a class="store-blog__read-link" href="{{ route('blog.posts.show', $post->slug) }}">ادامه مطلب <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" /></svg></a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    @if ($popularArticles->isNotEmpty())
                        <aside class="store-blog__sidebar" aria-label="مطالب منتخب">
                            <section class="store-blog__side-card" aria-labelledby="store-blog-popular-title">
                                <h2 class="store-blog__side-title" id="store-blog-popular-title">پیشنهاد برای مطالعه</h2>
                                <ol class="store-blog__popular-list">
                                    @foreach ($popularArticles as $post)
                                        <li>
                                            <a class="store-blog__popular-link" href="{{ route('blog.posts.show', $post->slug) }}">
                                                <span class="store-blog__popular-number">{{ sprintf('%02d', $loop->iteration) }}</span>
                                                <span><strong>{{ $post->title }}</strong><small>{{ $post->category->name }}</small></span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </section>
                        </aside>
                    @endif
                </div>
            @else
                <div class="store-blog__empty">
                    <h3>{{ $isSearching ? 'نتیجه‌ای برای این جستجو پیدا نشد.' : 'هنوز نوشته‌ای برای نمایش منتشر نشده است.' }}</h3>
                    <p>{{ $isSearching ? 'عبارت دیگری را امتحان کن یا موضوعات مجله را ببین.' : 'به‌زودی مطالب تازه در مجله '.site_name().' قرار می‌گیرند.' }}</p>
                    @if ($isSearching)
                        <a href="{{ route('blog.index') }}">پاک کردن جستجو</a>
                    @elseif ($selectedCategory)
                        <a href="{{ route('blog.index') }}#latest">مشاهده همه مطالب</a>
                    @endif
                </div>
            @endif
        </section>

        @if ($categories->isNotEmpty())
            <section class="store-blog__section" id="topics" aria-labelledby="store-blog-topics-title">
                <div class="store-blog__section-head">
                    <div>
                        <p>مسیر مطالعه‌ات را انتخاب کن</p>
                        <h2 id="store-blog-topics-title">موضوعات مجله</h2>
                    </div>
                    <span>هر موضوع، یک قدم نزدیک‌تر به انتخاب مناسب</span>
                </div>
                <div class="store-blog__topics-grid">
                    @foreach ($categories as $category)
                        <a class="store-blog__topic-card" href="{{ route('blog.categories.show', $category->slug) }}">
                            @if ($category->image)
                                <img class="store-blog__topic-image" src="{{ public_image_url($category->image) }}" alt="" loading="lazy">
                            @else
                                <span class="store-blog__topic-icon"><i class="ki-filled ki-book" aria-hidden="true"></i></span>
                            @endif
                            <span><strong>{{ $category->name }}</strong><small>{{ $category->description ?: 'مطالب و راهنماهای این موضوع' }}</small></span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="store-blog__closing" aria-labelledby="store-blog-closing-title">
            <div>
                <h2 id="store-blog-closing-title">برای انتخاب بعدی آماده‌ای؟</h2>
                <p>محصولات {{ site_name() }} را ببین و با اطلاعات کامل‌تر تصمیم بگیر.</p>
            </div>
            <a class="store-blog__button" href="{{ route('shop.index') }}">مشاهده فروشگاه</a>
        </section>
    </div>
</div>
