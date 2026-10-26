<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در برندها" placeholder="نام یا شناسه..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست برندها" description="برندهای فعال و غیرفعال فروشگاه و تعداد آیتم‌های مرتبط.">
        <x-slot:actions>
            <x-button href="{{ route('admin.brands.create') }}" size="sm" icon="ki-filled ki-plus">افزودن برند</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">نام برند</x-datatable-heading>
            <x-datatable-heading sortable sort="slug" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه متنی</x-datatable-heading>
            <x-datatable-heading>آیتم‌ها</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($brands as $brand)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $brand->id }}</td>
                <td class="px-5 py-3">
                    @if ($brand->image)
                        <img
                            src="{{ Storage::disk('public')->url($brand->image) }}"
                            alt="{{ $brand->name }}"
                            class="size-10 rounded-md border border-gray-200 object-cover"
                        />
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $brand->name }}</td>
                <td class="px-5 py-3 font-mono text-gray-600">{{ $brand->slug }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $brand->items_count }}</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$brand->status->color()" size="sm" dot>{{ $brand->status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($brand->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-button href="{{ route('admin.brands.edit', $brand->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">برندی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$brands" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
