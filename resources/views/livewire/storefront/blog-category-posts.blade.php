<div class="store-blog blog-page">
    <div class="store-blog__container">
        @include('blog.partials.navigation', ['activeCategory' => $category, 'searchWire' => true])

        <nav class="blog-page__breadcrumbs" aria-label="مسیر صفحه">
            <a href="{{ route('blog.index') }}">مجله {{ site_name() }}</a>
            <span aria-hidden="true">/</span>
            @if ($category->parent)
                <a href="{{ route('blog.categories.show', $category->parent->slug) }}">{{ $category->parent->name }}</a>
                <span aria-hidden="true">/</span>
            @endif
            <span aria-current="page">{{ $category->name }}</span>
        </nav>

        <header class="blog-page__category-hero">
            <div class="blog-page__category-copy">
                <span class="blog-page__eyebrow">موضوعات مجله {{ site_name() }}</span>
                <h1>{{ $category->name }}</h1>
                <p>{{ $category->description ?: 'نوشته‌ها و راهنماهای این موضوع را اینجا بخوانید.' }}</p>
            </div>
            <div class="blog-page__category-art">
                @if ($category->image)
                    <img src="{{ public_image_url($category->image) }}" alt="" fetchpriority="high">
                @else
                    <span aria-hidden="true"><i class="ki-filled ki-book-square"></i></span>
                @endif
            </div>
        </header>

        @if ($categories->isNotEmpty())
            <nav class="store-blog__topics blog-page__topics" aria-label="موضوعات دیگر">
                <span class="store-blog__topics-label">موضوعات دیگر</span>
                @foreach ($categories as $navigationCategory)
                    <a class="store-blog__topic" href="{{ route('blog.categories.show', $navigationCategory->slug) }}" @if ($navigationCategory->id === $category->id || $navigationCategory->id === $category->parent_id) aria-current="page" @endif>{{ $navigationCategory->name }}</a>
                @endforeach
            </nav>
        @endif

        <section class="blog-page__listing" aria-labelledby="blog-category-posts-title">
            <div class="blog-page__listing-head">
                <div>
                    <span class="blog-page__eyebrow">برای مطالعه</span>
                    <h2 id="blog-category-posts-title">{{ $search !== '' ? 'نتایج «'.$search.'»' : 'نوشته‌های '.$category->name }}</h2>
                </div>
                <div class="blog-page__sort" aria-label="مرتب‌سازی نوشته‌ها">
                    <button type="button" wire:click="setSort('latest')" aria-pressed="{{ $sort === 'latest' ? 'true' : 'false' }}">جدیدترین</button>
                    <button type="button" wire:click="setSort('popular')" aria-pressed="{{ $sort === 'popular' ? 'true' : 'false' }}">پربازدیدترین</button>
                </div>
            </div>

            @if ($posts->isNotEmpty())
                <div class="blog-page__cards">
                    @foreach ($posts as $post)
                        @include('blog.partials.post-card', ['post' => $post])
                    @endforeach
                </div>
                @if ($posts->hasPages())
                    <nav class="blog-page__pagination" aria-label="صفحه‌بندی نوشته‌ها">
                        <button type="button" wire:click="previousPage" @disabled($posts->onFirstPage())>صفحه قبل</button>
                        <span aria-current="page">صفحه {{ $posts->currentPage() }}</span>
                        <button type="button" wire:click="nextPage" @disabled(! $posts->hasMorePages())>صفحه بعد</button>
                    </nav>
                @endif
            @else
                <div class="store-blog__empty">
                    <h3>{{ $search !== '' ? 'نتیجه‌ای در این موضوع پیدا نشد.' : 'هنوز نوشته‌ای در این موضوع منتشر نشده است.' }}</h3>
                    <p>{{ $search !== '' ? 'عبارت دیگری را جستجو کن یا موضوعات دیگر را ببین.' : 'موضوعات دیگر مجله را ببینید.' }}</p>
                    <a href="{{ route('blog.index') }}">بازگشت به مجله</a>
                </div>
            @endif
        </section>
    </div>
</div>
