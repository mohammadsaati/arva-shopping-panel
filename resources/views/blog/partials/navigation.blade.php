<nav class="store-blog__bar" aria-label="منوی مجله {{ site_name() }}">
    <a class="store-blog__brand" href="{{ route('blog.index') }}" aria-label="مجله {{ site_name() }}، صفحه اصلی">
        <span class="store-blog__brand-mark" aria-hidden="true">{{ mb_substr(site_name(), 0, 1) }}</span>
        <span>مجله {{ site_name() }}</span>
    </a>
    <div class="store-blog__nav">
        <a href="{{ route('blog.index') }}" @if (! isset($activeCategory)) aria-current="page" @endif>مجله</a>
        @foreach ($categories->take(4) as $navigationCategory)
            <a href="{{ route('blog.categories.show', $navigationCategory->slug) }}" @if (isset($activeCategory) && ($activeCategory->id === $navigationCategory->id || $activeCategory->parent_id === $navigationCategory->id)) aria-current="page" @endif>{{ $navigationCategory->name }}</a>
        @endforeach
    </div>
    <div class="store-blog__bar-actions">
        @if (! empty($searchWire))
            <label class="store-blog__search">
                <span class="sr-only">جستجو در مجله</span>
                <i class="ki-filled ki-magnifier" aria-hidden="true"></i>
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="جستجو در مجله..." autocomplete="off">
            </label>
        @else
            <form class="store-blog__search" action="{{ route('blog.index') }}" method="get" role="search">
                <label class="sr-only" for="blog-search">جستجو در مجله</label>
                <i class="ki-filled ki-magnifier" aria-hidden="true"></i>
                <input id="blog-search" type="search" name="q" value="{{ $searchValue ?? '' }}" placeholder="جستجو در مجله..." autocomplete="off">
            </form>
        @endif
        <a class="store-blog__back" href="{{ route('shop.index') }}">
            <span>فروشگاه</span>
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m0 0 6 6m-6-6 6-6" /></svg>
        </a>
    </div>
</nav>
