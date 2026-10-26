<div class="store-catalog__filter-controls">
    @if ($fixedCategoryId === null)
        <details class="store-catalog__filter-group" open>
            <summary><span><i class="ki-filled ki-category" aria-hidden="true"></i> دسته‌بندی</span>@if ($categoryId !== '') <span class="store-catalog__group-dot" aria-label="فیلتر فعال"></span> @endif</summary>
            <div class="store-catalog__filter-group-body">
                <x-user.filter-select id="catalog-category-{{ $controlId }}" label="دسته‌بندی" placeholder="همه دسته‌بندی‌ها" wire:model.live="categoryId">
                    @foreach ($categories as $category)
                        @if ($category->children->isNotEmpty())
                            <optgroup label="{{ $category->name }}">
                                <option value="{{ $category->id }}" @selected($categoryId === (string) $category->id)>همه {{ $category->name }}</option>
                                @foreach ($category->children as $childCategory)
                                    <option value="{{ $childCategory->id }}" @selected($categoryId === (string) $childCategory->id)>{{ $childCategory->name }}</option>
                                @endforeach
                            </optgroup>
                        @else
                            <option value="{{ $category->id }}" @selected($categoryId === (string) $category->id)>{{ $category->name }}</option>
                        @endif
                    @endforeach
                </x-user.filter-select>
                <p class="store-catalog__filter-hint">نام دسته یا زیر‌دسته را جستجو کنید.</p>
            </div>
        </details>
    @endif

    <details class="store-catalog__filter-group" open>
        <summary><span><i class="ki-filled ki-price-tag" aria-hidden="true"></i> برند</span>@if ($brandId !== '') <span class="store-catalog__group-dot" aria-label="فیلتر فعال"></span> @endif</summary>
        <div class="store-catalog__filter-group-body">
            <x-user.filter-select id="catalog-brand-{{ $controlId }}" label="برند" placeholder="همه برندها" wire:model.live="brandId">
                @foreach ($brands as $brandOptionId => $brandName)
                    <option value="{{ $brandOptionId }}" @selected($brandId === (string) $brandOptionId)>{{ $brandName }}</option>
                @endforeach
            </x-user.filter-select>
        </div>
    </details>

    <details class="store-catalog__filter-group" open>
        <summary><span><i class="ki-filled ki-dollar" aria-hidden="true"></i> محدوده قیمت</span>@if ($minPrice !== '' || $maxPrice !== '') <span class="store-catalog__group-dot" aria-label="فیلتر فعال"></span> @endif</summary>
        <div class="store-catalog__filter-group-body">
            <div class="store-catalog__price-fields">
                <label class="store-catalog__filter-field" for="catalog-min-price-{{ $controlId }}">
                    <span>از قیمت</span>
                    <span class="store-catalog__price-input"><input id="catalog-min-price-{{ $controlId }}" type="number" min="0" inputmode="numeric" wire:model.live.debounce.500ms="minPrice" placeholder="۰"><span>تومان</span></span>
                </label>
                <label class="store-catalog__filter-field" for="catalog-max-price-{{ $controlId }}">
                    <span>تا قیمت</span>
                    <span class="store-catalog__price-input"><input id="catalog-max-price-{{ $controlId }}" type="number" min="0" inputmode="numeric" wire:model.live.debounce.500ms="maxPrice" placeholder="بدون سقف"><span>تومان</span></span>
                </label>
            </div>
            @if ($minPrice !== '' && $maxPrice !== '' && ctype_digit($minPrice) && ctype_digit($maxPrice) && (int) $minPrice > (int) $maxPrice)
                <p class="store-catalog__price-error" role="alert">حداقل قیمت باید از حداکثر کمتر باشد.</p>
            @endif
        </div>
    </details>

    <label class="store-catalog__availability" for="catalog-available-{{ $controlId }}">
        <span><strong>فقط کالاهای موجود</strong><small>کالاهای ناموجود را پنهان کن</small></span>
        <input id="catalog-available-{{ $controlId }}" type="checkbox" wire:model.live="inStockOnly">
    </label>
</div>
