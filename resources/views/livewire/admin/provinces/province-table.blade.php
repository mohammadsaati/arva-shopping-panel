<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در استان‌ها" placeholder="نام استان..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست استان‌ها" description="استان‌های ثبت‌شده و تعداد شهرهای مرتبط با هرکدام.">
        <x-slot:actions>
            <x-button href="{{ route('admin.provinces.create') }}" size="sm" icon="ki-filled ki-plus">افزودن استان</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">استان</x-datatable-heading>
            <x-datatable-heading>شهرها</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-16 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($provinces as $province)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $province->id }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $province->name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $province->cities_count }}</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$province->status->color()" size="sm" dot>{{ $province->status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($province->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات استان ' . $province->name">
                        <div class="menu-item">
                            <a href="{{ route('admin.provinces.edit', $province->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">ویرایش</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.provinces.show', $province->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-eye" aria-hidden="true"></i></span>
                                <span class="menu-title">مشاهده</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">استانی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$provinces" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
