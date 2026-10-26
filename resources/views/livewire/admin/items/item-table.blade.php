<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در آیتم‌ها" placeholder="نام، کد یا شناسه..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="آیتم‌ها" description="هر آیتم می‌تواند چند محصول با رنگ و سایز متفاوت داشته باشد.">
        <x-slot:actions>
            <x-button href="{{ route('admin.items.create') }}" size="sm" icon="ki-filled ki-plus">افزودن آیتم</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">نام آیتم</x-datatable-heading>
            <x-datatable-heading sortable sort="code" :sort-by="$sortBy" :sort-dir="$sortDir">کد</x-datatable-heading>
            <x-datatable-heading>دسته‌بندی / برند</x-datatable-heading>
            <x-datatable-heading>محصولات</x-datatable-heading>
            <x-datatable-heading>پیشنهاد شگفت‌انگیز</x-datatable-heading>
            <x-datatable-heading>پایان پیشنهاد</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($items as $item)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $item->id }}</td>
                <td class="px-5 py-3">
                    <span class="font-medium text-gray-900">{{ $item->name }}</span>
                    <span class="mt-0.5 block text-xs text-gray-400">{{ $item->slug }}</span>
                </td>
                <td class="px-5 py-3 font-mono text-gray-600">{{ $item->code }}</td>
                <td class="px-5 py-3 text-gray-600">
                    {{ $item->category?->name ?? '—' }}
                    <span class="block text-xs text-gray-400">{{ $item->brand?->name ?? 'بدون برند' }}</span>
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('admin.products.index', ['item_id' => $item->id]) }}" class="text-primary hover:underline">
                        {{ $item->products_count }} محصول
                    </a>
                </td>
                <td class="px-5 py-3">
                    <x-badge :variant="$item->is_amazing ? 'success' : 'default'" size="sm">
                        {{ $item->is_amazing ? 'فعال' : 'غیرفعال' }}
                    </x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">
                    {{ $item->amazing_end_time ? jalali_date($item->amazing_end_time, 'Y/m/d H:i') : '—' }}
                </td>
                <td class="px-5 py-3">
                    <x-badge :variant="$item->status->color()" size="sm" dot>{{ $item->status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($item->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات آیتم ' . $item->name">
                        <div class="menu-item">
                            <a href="{{ route('admin.items.edit', $item->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">ویرایش</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.items.show', $item->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-eye" aria-hidden="true"></i></span>
                                <span class="menu-title">مشاهده</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="10" class="px-5 py-12 text-center text-gray-400">آیتمی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$items" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
