<div class="sm:col-span-2">
    <input type="hidden" name="product_ids_present" value="1" />
    <input type="hidden" name="category_ids_present" value="1" />
    <input type="hidden" name="user_id" value="{{ $selectedUserId ?? '' }}" />
    @foreach ($selectedProductIds as $productId)
        <input type="hidden" name="product_ids[]" value="{{ $productId }}" wire:key="discount-product-value-{{ $productId }}" />
    @endforeach
    @foreach ($selectedCategoryIds as $categoryId)
        <input type="hidden" name="category_ids[]" value="{{ $categoryId }}" wire:key="discount-category-value-{{ $categoryId }}" />
    @endforeach

    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="font-medium text-gray-900">محدودیت کاربر، کالا و دسته‌بندی</h3>
            <p class="mt-1 text-xs text-gray-500">کاربر را برای اختصاصی‌کردن کد انتخاب کنید. اگر کاربری انتخاب نشود، کد برای همه مشتریان است. محدودیت‌های محصول و دسته‌بندی نیز با هم به‌صورت «هرکدام منطبق باشد» محاسبه می‌شوند.</p>
        </div>
        <span class="badge badge-primary" aria-live="polite">{{ count($selectedProductIds) }} محصول · {{ count($selectedCategoryIds) }} دسته‌بندی · {{ $selectedUserId ? '۱ کاربر' : 'همه کاربران' }}</span>
    </div>

    <div class="mb-3 flex flex-wrap gap-2" role="tablist" aria-label="نوع موارد مشمول تخفیف">
        <button
            type="button"
            wire:click="showProducts"
            role="tab"
            aria-selected="{{ $targetType === 'products' ? 'true' : 'false' }}"
            class="btn btn-sm {{ $targetType === 'products' ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-2"
        >
            <i class="ki-filled ki-package" aria-hidden="true"></i>
            محصولات
            <span class="badge badge-sm {{ $targetType === 'products' ? 'badge-light' : 'badge-primary' }}">{{ count($selectedProductIds) }}</span>
        </button>
        <button
            type="button"
            wire:click="showCategories"
            role="tab"
            aria-selected="{{ $targetType === 'categories' ? 'true' : 'false' }}"
            class="btn btn-sm {{ $targetType === 'categories' ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-2"
        >
            <i class="ki-filled ki-category" aria-hidden="true"></i>
            دسته‌بندی‌ها
            <span class="badge badge-sm {{ $targetType === 'categories' ? 'badge-light' : 'badge-primary' }}">{{ count($selectedCategoryIds) }}</span>
        </button>
        <button
            type="button"
            wire:click="showUsers"
            role="tab"
            aria-selected="{{ $targetType === 'users' ? 'true' : 'false' }}"
            class="btn btn-sm {{ $targetType === 'users' ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-2"
        >
            <i class="ki-filled ki-user" aria-hidden="true"></i>
            کاربر
            <span class="badge badge-sm {{ $targetType === 'users' ? 'badge-light' : 'badge-primary' }}">{{ $selectedUserId ? '۱' : '۰' }}</span>
        </button>
    </div>

    @if ($selectedUserId)
        <div class="mb-3 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-primary/20 bg-primary/5 px-4 py-3">
            <span class="text-sm text-gray-700">این کد به حساب کاربری <strong dir="ltr">#{{ $selectedUserId }}</strong> اختصاص دارد.</span>
            <button type="button" wire:click="clearUserSelection" class="btn btn-sm btn-light inline-flex items-center gap-1.5">
                <i class="ki-filled ki-cross" aria-hidden="true"></i>
                عمومی‌کردن کد
            </button>
        </div>
    @endif

    <div class="card mb-3">
        <x-admin.filter-panel
            embedded
            class="card-body"
            title="جستجو و محدود کردن فهرست"
            :active-count="$this->activeFilterCount"
        >
            <x-slot:actions>
                <button
                    type="button"
                    wire:click="toggleSelectedOnly"
                    aria-pressed="{{ $selectedOnly ? 'true' : 'false' }}"
                    class="btn btn-sm {{ $selectedOnly ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-1.5"
                >
                    <i class="ki-filled ki-check-circle" aria-hidden="true"></i>
                    فقط انتخاب‌شده‌ها
                </button>
                <span wire:loading wire:target="search,toggleSelectedOnly,toggleTarget,clearUserSelection,perPage,previousPage,nextPage,showProducts,showCategories,showUsers" class="text-xs text-gray-500" role="status">در حال بارگذاری...</span>
            </x-slot:actions>

            <x-admin.filter-search
                :label="match ($targetType) {
                    'products' => 'جستجو در محصولات',
                    'categories' => 'جستجو در دسته‌بندی‌ها',
                    default => 'جستجو در کاربران',
                }"
                :placeholder="match ($targetType) {
                    'products' => 'نام محصول، کد، رنگ، سایز یا شناسه...',
                    'categories' => 'نام، شناسه یا نامک دسته‌بندی...',
                    default => 'نام، شماره تلفن یا شناسه کاربر...',
                }"
            />
        </x-admin.filter-panel>
    </div>

    <x-datatable
        :title="match ($targetType) { 'products' => 'انتخاب محصولات', 'categories' => 'انتخاب دسته‌بندی‌ها', default => 'انتخاب کاربر' }"
        :description="match ($targetType) { 'products' => 'محصول‌های موردنظر را جستجو و انتخاب کنید.', 'categories' => 'دسته‌بندی‌های موردنظر را جستجو و انتخاب کنید.', default => 'کاربری را که کد تخفیف برای او اختصاصی است انتخاب کنید.' }"
    >
        <x-slot:head>
            <x-datatable-heading class="w-32">انتخاب</x-datatable-heading>
            @if ($targetType === 'products')
                <x-datatable-heading>محصول</x-datatable-heading>
                <x-datatable-heading>شناسه</x-datatable-heading>
                <x-datatable-heading>قیمت</x-datatable-heading>
                <x-datatable-heading>موجودی</x-datatable-heading>
                <x-datatable-heading>وضعیت</x-datatable-heading>
            @elseif ($targetType === 'categories')
                <x-datatable-heading>دسته‌بندی</x-datatable-heading>
                <x-datatable-heading>نامک</x-datatable-heading>
                <x-datatable-heading>محصول‌ها</x-datatable-heading>
                <x-datatable-heading>وضعیت</x-datatable-heading>
            @else
                <x-datatable-heading>کاربر</x-datatable-heading>
                <x-datatable-heading>شناسه</x-datatable-heading>
                <x-datatable-heading>شماره تماس</x-datatable-heading>
                <x-datatable-heading>نوع</x-datatable-heading>
                <x-datatable-heading>وضعیت</x-datatable-heading>
            @endif
        </x-slot:head>

        @forelse ($targets as $target)
            @php($isSelected = in_array($target->id, $selectedIds, true))
            <tr wire:key="discount-{{ $targetType }}-row-{{ $target->id }}" class="border-b border-gray-100 transition-colors hover:bg-primary/5 {{ $isSelected ? 'bg-primary/5' : '' }}">
                <td class="px-5 py-3">
                    <button
                        type="button"
                        wire:click="toggleTarget({{ $target->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleTarget"
                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                        aria-label="{{ $isSelected ? 'حذف انتخاب' : 'انتخاب' }} {{ $targetType === 'products' ? ($target->item->name ?? 'محصول') : ($targetType === 'categories' ? $target->name : $target->first_name . ' ' . $target->last_name) }}"
                        class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-1"
                    >
                        <i class="ki-filled {{ $isSelected ? 'ki-check' : 'ki-plus' }}" aria-hidden="true"></i>
                        {{ $isSelected ? 'انتخاب شده' : 'انتخاب' }}
                    </button>
                </td>

                @if ($targetType === 'products')
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ public_image_url($target->item->image) }}" alt="" class="size-10 shrink-0 rounded-md border border-gray-200 object-cover" loading="lazy" />
                            <div>
                                <span class="font-medium text-gray-900">{{ $target->item->name }}</span>
                                <span class="block text-xs text-gray-500">
                                    {{ collect([$target->color?->name, $target->size?->value])->filter()->implode(' / ') ?: 'بدون رنگ و سایز' }}
                                </span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-gray-500">#{{ $target->id }}</td>
                    <td class="px-5 py-3 whitespace-nowrap">{{ number_format($target->price) }} تومان</td>
                    <td class="px-5 py-3">{{ number_format($target->stock) }}</td>
                    <td class="px-5 py-3"><x-badge :variant="$target->status->color()" size="sm" dot>{{ $target->status->trans() }}</x-badge></td>
                @elseif ($targetType === 'categories')
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            @if ($target->image)
                                <img src="{{ public_image_url($target->image) }}" alt="" class="size-10 shrink-0 rounded-md border border-gray-200 object-cover" loading="lazy" />
                            @endif
                            <div>
                                <span class="font-medium text-gray-900">{{ $target->name }}</span>
                                <span class="block text-xs text-gray-500">{{ $target->parent?->name ? 'زیرمجموعه ' . $target->parent->name : 'دسته‌بندی اصلی' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs text-gray-500" dir="ltr">{{ $target->slug }}</td>
                    <td class="px-5 py-3">{{ number_format($target->items_count) }}</td>
                    <td class="px-5 py-3"><x-badge :variant="$target->status->color()" size="sm" dot>{{ $target->status->trans() }}</x-badge></td>
                @else
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $target->first_name }} {{ $target->last_name }}</td>
                    <td class="px-5 py-3 font-mono text-gray-500">#{{ $target->id }}</td>
                    <td class="px-5 py-3 font-mono text-gray-600" dir="ltr">{{ $target->phone }}</td>
                    <td class="px-5 py-3"><x-badge variant="info" size="sm">{{ $target->type->trans() }}</x-badge></td>
                    <td class="px-5 py-3"><x-badge :variant="$target->status->color()" size="sm" dot>{{ $target->status->trans() }}</x-badge></td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ $targetType === 'products' ? 6 : ($targetType === 'categories' ? 5 : 6) }}" class="px-5 py-10 text-center text-gray-500">
                    {{ $selectedOnly ? 'موردی برای نمایش پیدا نشد.' : 'موردی با این جستجو پیدا نشد.' }}
                </td>
            </tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$targets" :per-page="$perPage" :per-page-options="[10, 15, 25, 50]" />

    @if ($selectionError)
        <p class="mt-2 text-xs text-danger" role="alert">{{ $selectionError }}</p>
    @endif
    @if ($errors->has('product_ids') || $errors->has('product_ids.*'))
        <p class="mt-2 text-xs text-danger" role="alert">{{ $errors->first('product_ids') ?: $errors->first('product_ids.*') }}</p>
    @endif
    @if ($errors->has('category_ids') || $errors->has('category_ids.*'))
        <p class="mt-2 text-xs text-danger" role="alert">{{ $errors->first('category_ids') ?: $errors->first('category_ids.*') }}</p>
    @endif
    @error('user_id')
        <p class="mt-2 text-xs text-danger" role="alert">{{ $message }}</p>
    @enderror
</div>
