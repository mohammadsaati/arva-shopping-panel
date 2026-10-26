<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در محصولات" placeholder="نام یا کد آیتم..." />
        <x-admin.filter-field label="آیتم">
            <x-form.select2
                name="itemId"
                :options="$itemOptions"
                :selected="$itemId"
                :route="route('admin.items.select-options')"
                placeholder="همه آیتم‌ها"
                class="w-full"
                wire:model.live="itemId"
            />
        </x-admin.filter-field>
        <x-admin.filter-select label="موجودی" empty-label="همه موجودی‌ها" wire:model.live="stockFilter">
            @foreach ($stockFilterOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="رنگ" empty-label="همه رنگ‌ها" wire:model.live="colorId">
            @foreach ($colorOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="سایز" empty-label="همه سایزها" wire:model.live="sizeId">
            @foreach ($sizeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        @if ($stockFilter === \App\Enums\ProductStockFilterEnum::LOW_STOCK->value)
            <x-slot:footer>
                موجودی کم یعنی بین ۱ تا {{ number_format($lowStockThreshold) }} عدد.
            </x-slot:footer>
        @endif
    </x-admin.filter-panel>

    <x-datatable title="محصولات" description="هر سطر یک محصول یا تنوع متصل به آیتم است.">
        <x-slot:actions>
            <x-button href="{{ route('admin.products.create', $itemId !== '' ? ['item_id' => $itemId] : []) }}" size="sm" icon="ki-filled ki-plus">افزودن محصول</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="item_id" :sort-by="$sortBy" :sort-dir="$sortDir">آیتم</x-datatable-heading>
            <x-datatable-heading>رنگ / سایز</x-datatable-heading>
            <x-datatable-heading sortable sort="price" :sort-by="$sortBy" :sort-dir="$sortDir">قیمت</x-datatable-heading>
            <x-datatable-heading sortable sort="stock" :sort-by="$sortBy" :sort-dir="$sortDir">موجودی</x-datatable-heading>
            <x-datatable-heading sortable sort="shipping_price" :sort-by="$sortBy" :sort-dir="$sortDir">ارسال پیش‌فرض</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($products as $product)
            @php
                $stockVariant = match (true) {
                    $product->stock === 0 => 'danger',
                    $product->stock <= $lowStockThreshold => 'warning',
                    default => 'success',
                };
            @endphp
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $product->id }}</td>
                <td class="px-5 py-3">
                    <a href="{{ route('admin.items.show', $product->item_id) }}" class="font-medium text-primary hover:underline">{{ $product->item->name }}</a>
                    <span class="mt-0.5 block text-xs text-gray-400">کد {{ $product->item->code }}</span>
                </td>
                <td class="px-5 py-3 text-gray-600">
                    {{ $product->color?->name ?? 'بدون رنگ' }}
                    <span class="block text-xs text-gray-400">{{ $product->size?->value ?? 'بدون سایز' }}</span>
                </td>
                <td class="px-5 py-3 text-gray-700">{{ number_format($product->price) }} تومان</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$stockVariant" size="sm">{{ number_format($product->stock) }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-700">{{ number_format($product->shipping_price) }} تومان</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$product->status->color()" size="sm" dot>{{ $product->status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($product->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات محصول ' . $product->id">
                        <div class="menu-item">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">ویرایش محصول</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.items.edit', $product->item_id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-setting-2" aria-hidden="true"></i></span>
                                <span class="menu-title">مشخصات کالا</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="px-5 py-12 text-center text-gray-400">محصولی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$products" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
