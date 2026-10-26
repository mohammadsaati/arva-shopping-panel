<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در اسلایدرها" placeholder="عنوان یا مقصد..." />
        <x-admin.filter-select label="نوع مقصد" empty-label="همه نوع‌ها" wire:model.live="type">
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

    <x-datatable title="فهرست اسلایدرها" description="اسلایدهای تصویری، اولویت نمایش و مقصد هرکدام.">
        <x-slot:actions><x-button href="{{ route('admin.sliders.create') }}" size="sm" icon="ki-filled ki-plus">افزودن اسلایدر</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="title" :sort-by="$sortBy" :sort-dir="$sortDir">عنوان</x-datatable-heading>
            <x-datatable-heading sortable sort="priority" :sort-by="$sortBy" :sort-dir="$sortDir">اولویت</x-datatable-heading>
            <x-datatable-heading sortable sort="type" :sort-by="$sortBy" :sort-dir="$sortDir">نوع مقصد</x-datatable-heading>
            <x-datatable-heading>مقصد</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($sliders as $slider)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $slider->id }}</td>
                <td class="px-5 py-3"><img src="{{ Storage::disk('public')->url($slider->image) }}" alt="{{ $slider->title }}" class="h-12 w-20 rounded-md border border-gray-200 object-cover" /></td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $slider->title }}</td>
                <td class="px-5 py-3">{{ $slider->priority }}</td>
                <td class="px-5 py-3">{{ $slider->type->trans() }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $slider->product?->item?->name ?? $slider->category?->name ?? $slider->section?->name ?? $slider->link ?? '—' }}</td>
                <td class="px-5 py-3"><x-badge :variant="$slider->status->color()" size="sm" dot>{{ $slider->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($slider->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end"><x-button href="{{ route('admin.sliders.edit', $slider->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button></td>
            </tr>
        @empty
            <tr><td colspan="9" class="px-5 py-12 text-center text-gray-400">اسلایدری با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$sliders" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
