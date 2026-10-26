<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در شهرها" placeholder="نام شهر، شناسه یا استان..." />
        <x-admin.filter-select label="استان" empty-label="همه استان‌ها" wire:model.live="provinceId">
            @foreach ($provinceOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست شهرها" description="شهرهای ثبت‌شده به‌همراه استان و وضعیت آن‌ها.">
        <x-slot:actions>
            <x-button href="{{ route('admin.cities.create') }}" size="sm" icon="ki-filled ki-plus">افزودن شهر</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">شهر</x-datatable-heading>
            <x-datatable-heading sortable sort="province_id" :sort-by="$sortBy" :sort-dir="$sortDir">استان</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-16 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($cities as $city)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $city->id }}</td>
                <td class="px-5 py-3">
                    <span class="font-medium text-gray-900">{{ $city->name }}</span>
                    <span class="mt-0.5 block text-xs text-gray-400">{{ $city->slug }}</span>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $city->province->name }}</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$city->status->color()" size="sm" dot>{{ $city->status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($city->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات شهر ' . $city->name">
                        <div class="menu-item">
                            <a href="{{ route('admin.cities.edit', $city->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">ویرایش</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.cities.show', $city->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-eye" aria-hidden="true"></i></span>
                                <span class="menu-title">مشاهده</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">شهری با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$cities" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
