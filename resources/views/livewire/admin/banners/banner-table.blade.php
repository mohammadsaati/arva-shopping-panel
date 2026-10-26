<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در بنرها" placeholder="جستجو در مقصد یا شناسه..." />
        <x-admin.filter-select label="نوع بنر" empty-label="همه نوع‌ها" wire:model.live="type">
            @foreach ($typeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست بنرها" description="بنرهای تصویری فروشگاه و مقصد هرکدام.">
        <x-slot:actions><x-button href="{{ route('admin.banners.create') }}" size="sm" icon="ki-filled ki-plus">افزودن بنر</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="type" :sort-by="$sortBy" :sort-dir="$sortDir">نوع</x-datatable-heading>
            <x-datatable-heading>مقصد</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($banners as $banner)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $banner->id }}</td>
                <td class="px-5 py-3"><img src="{{ Storage::disk('public')->url($banner->image) }}" alt="بنر {{ $banner->id }}" class="h-12 w-20 rounded-md border border-gray-200 object-cover" /></td>
                <td class="px-5 py-3">{{ $banner->type->trans() }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $banner->product?->item?->name ?? $banner->category?->name ?? $banner->link ?? '—' }}</td>
                <td class="px-5 py-3"><x-badge :variant="$banner->status->color()" size="sm" dot>{{ $banner->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($banner->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end"><x-button href="{{ route('admin.banners.edit', $banner->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">بنری با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$banners" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
