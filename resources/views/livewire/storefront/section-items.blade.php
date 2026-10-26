<div class="store-section-page__catalog">
    <div class="store-section-page__toolbar">
        <label class="store-catalog__search">
            <span class="sr-only">جستجو در این بخش</span>
            <i class="ki-filled ki-magnifier" aria-hidden="true"></i>
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="جستجو در محصولات این بخش..." autocomplete="off">
        </label>
        <div class="store-section-page__sort" aria-label="مرتب‌سازی محصولات بخش">
            <span>مرتب‌سازی:</span>
            <button type="button" wire:click="setSort('latest')" @class(['is-active' => $sort === 'latest']) aria-pressed="{{ $sort === 'latest' ? 'true' : 'false' }}">جدیدترین</button>
            <button type="button" wire:click="setSort('name_asc')" @class(['is-active' => $sort === 'name_asc']) aria-pressed="{{ $sort === 'name_asc' ? 'true' : 'false' }}">نام کالا</button>
        </div>
    </div>

    @if ($items->isNotEmpty())
        <div class="store-product-grid store-section-page__grid">
            @foreach ($items as $item)
                @if ($item->products->first())
                    <x-user.product-card :product="$item->products->first()" />
                @endif
            @endforeach
        </div>

        <nav class="store-catalog__pagination" aria-label="{{ __('pagination.page', ['page' => $items->currentPage()]) }}">
            @if (! $items->onFirstPage())
                <button type="button" wire:click="previousPage" wire:loading.attr="disabled">{{ __('pagination.previous') }}</button>
            @endif
            <span aria-current="page">{{ __('pagination.page', ['page' => $items->currentPage()]) }}</span>
            @if ($items->hasMorePages())
                <button type="button" wire:click="nextPage" wire:loading.attr="disabled">{{ __('pagination.next') }}</button>
            @endif
            <label>
                <span>{{ __('pagination.show') }}</span>
                <select wire:model.live="perPage" aria-label="{{ __('pagination.per_page') }}">
                    @foreach ([12, 24, 36] as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
                <span>{{ __('pagination.per_page') }}</span>
            </label>
        </nav>
    @else
        <div class="store-empty-state">
            <span class="store-empty-state__icon" aria-hidden="true">◎</span>
            <h2>محصولی در این بخش پیدا نشد</h2>
            <p>برای دیدن همه محصولات این بخش، جستجو را پاک کنید.</p>
            @if ($search !== '')
                <button type="button" class="store-shop__home-link" wire:click="clearSearch">پاک کردن جستجو</button>
            @endif
        </div>
    @endif
</div>
