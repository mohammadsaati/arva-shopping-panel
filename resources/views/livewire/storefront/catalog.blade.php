<div class="store-catalog">
    <div class="store-catalog__layout">
        <aside class="store-catalog__sidebar" aria-label="فیلترهای فروشگاه">
            <div class="store-catalog__sidebar-heading">
                <div><span class="store-catalog__sidebar-eyebrow">انتخاب دقیق‌تر</span><h2><i class="ki-filled ki-filter-square" aria-hidden="true"></i> فیلترها @if ($activeFilterCount > 0)<small>{{ $activeFilterCount }}</small>@endif</h2></div>
                @if ($activeFilterCount > 0)
                    <button type="button" wire:click="clearFilters">پاک کردن همه</button>
                @endif
            </div>
            @include('livewire.storefront.partials.catalog-filter-controls', ['controlId' => 'desktop'])
        </aside>

        <div class="store-catalog__results">
            <label class="store-catalog__search">
                <span class="sr-only">جستجوی محصولات</span>
                <i class="ki-filled ki-magnifier" aria-hidden="true"></i>
                <input type="search" wire:model.live.debounce.400ms="search" placeholder="نام کالا یا کد آیتم را بنویسید..." autocomplete="off">
            </label>

            <div class="store-catalog__mobile-actions">
                <button type="button" wire:click="openMobilePanel('sort')" aria-haspopup="dialog">
                    <i class="ki-filled ki-sort" aria-hidden="true"></i>
                    <span>مرتب‌سازی: {{ match ($sort) { 'price_asc' => 'ارزان‌ترین', 'price_desc' => 'گران‌ترین', default => 'جدیدترین' } }}</span>
                </button>
                <button type="button" wire:click="openMobilePanel('filters')" aria-haspopup="dialog">
                    <i class="ki-filled ki-filter-square" aria-hidden="true"></i>
                    <span>فیلترها</span>
                    @if ($activeFilterCount > 0)
                        <span class="store-catalog__filter-count">{{ $activeFilterCount }}</span>
                    @endif
                </button>
                <span class="store-catalog__mobile-loading" wire:loading role="status">در حال به‌روزرسانی محصولات...</span>
            </div>

            @if ($activeFilters !== [])
                <div class="store-catalog__active-filters" aria-label="فیلترهای انتخاب‌شده">
                    <span>انتخاب‌های شما</span>
                    @foreach ($activeFilters as $filterKey => $filterLabel)
                        <button type="button" wire:click="removeFilter('{{ $filterKey }}')" wire:key="active-filter-{{ $filterKey }}" aria-label="حذف فیلتر {{ $filterLabel }}">
                            {{ $filterLabel }} <span aria-hidden="true">×</span>
                        </button>
                    @endforeach
                    <button type="button" class="store-catalog__active-clear" wire:click="clearFilters">پاک کردن همه</button>
                </div>
            @endif

            <div class="store-catalog__sortbar" aria-label="مرتب‌سازی محصولات">
                <span>مرتب‌سازی:</span>
                <button type="button" wire:click="setSort('latest')" @class(['is-active' => $sort === 'latest']) aria-pressed="{{ $sort === 'latest' ? 'true' : 'false' }}">جدیدترین</button>
                <button type="button" wire:click="setSort('price_asc')" @class(['is-active' => $sort === 'price_asc']) aria-pressed="{{ $sort === 'price_asc' ? 'true' : 'false' }}">ارزان‌ترین</button>
                <button type="button" wire:click="setSort('price_desc')" @class(['is-active' => $sort === 'price_desc']) aria-pressed="{{ $sort === 'price_desc' ? 'true' : 'false' }}">گران‌ترین</button>
                <span wire:loading class="store-catalog__loading" role="status">در حال بارگذاری...</span>
            </div>

            <div class="store-catalog__summary">
                <h2>{{ $fixedCategoryId !== null ? 'محصولات این دسته‌بندی' : ($categoryId !== '' ? 'محصولات دسته‌بندی انتخاب‌شده' : 'همه محصولات') }}</h2>
                @if ($search !== '')
                    <p>نتایج جستجو برای «{{ $search }}»</p>
                @else
                    <p>{{ $inStockOnly ? 'محصولات موجود فروشگاه' : 'همه محصولات فروشگاه' }}</p>
                @endif
            </div>

            @if ($items->isNotEmpty())
                <div class="store-product-grid store-catalog__grid">
                    @foreach ($items as $item)
                        <x-user.product-card :item="$item" :show-variant="true" />
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
                    <h3>محصولی با این مشخصات پیدا نشد</h3>
                    <p>فیلترها را تغییر دهید یا دوباره همه محصولات را ببینید.</p>
                    <button type="button" class="store-shop__home-link" wire:click="clearFilters">نمایش همه محصولات</button>
                </div>
            @endif
        </div>
    </div>

    @if ($mobilePanel !== '')
        <div class="store-catalog__mobile-overlay">
            <button type="button" class="store-catalog__backdrop" wire:click="closeMobilePanel" aria-label="بستن پنجره"></button>
            <section class="store-catalog__sheet" role="dialog" aria-modal="true" aria-label="{{ $mobilePanel === 'filters' ? 'فیلترهای فروشگاه' : 'مرتب‌سازی محصولات' }}" tabindex="-1" wire:keydown.escape="closeMobilePanel" x-init="$el.focus()">
                <header class="store-catalog__sheet-header">
                    <h2>{{ $mobilePanel === 'filters' ? 'فیلترها' : 'مرتب‌سازی بر اساس' }}</h2>
                    <button type="button" wire:click="closeMobilePanel" aria-label="بستن">
                        <i class="ki-filled ki-cross" aria-hidden="true"></i>
                    </button>
                </header>
                @if ($mobilePanel === 'filters')
                    <div class="store-catalog__sheet-body">
                        @include('livewire.storefront.partials.catalog-filter-controls', ['controlId' => 'mobile'])
                    </div>
                    <footer class="store-catalog__sheet-footer">
                        @if ($activeFilterCount > 0)
                            <button type="button" class="store-catalog__sheet-clear" wire:click="clearFilters">پاک کردن همه</button>
                        @endif
                        <button type="button" class="store-catalog__sheet-apply" wire:click="closeMobilePanel">نمایش محصولات</button>
                    </footer>
                @else
                    <div class="store-catalog__sheet-sort">
                        <button type="button" wire:click="setSort('latest')" @class(['is-active' => $sort === 'latest']) aria-pressed="{{ $sort === 'latest' ? 'true' : 'false' }}">جدیدترین</button>
                        <button type="button" wire:click="setSort('price_asc')" @class(['is-active' => $sort === 'price_asc']) aria-pressed="{{ $sort === 'price_asc' ? 'true' : 'false' }}">ارزان‌ترین</button>
                        <button type="button" wire:click="setSort('price_desc')" @class(['is-active' => $sort === 'price_desc']) aria-pressed="{{ $sort === 'price_desc' ? 'true' : 'false' }}">گران‌ترین</button>
                    </div>
                @endif
            </section>
        </div>
    @endif
</div>
