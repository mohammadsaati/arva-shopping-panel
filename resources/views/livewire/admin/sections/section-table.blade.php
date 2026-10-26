<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در بخش‌ها" placeholder="نام یا شناسه..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست بخش‌ها" description="بخش‌های محتوایی فروشگاه و تعداد تصاویر و اسلایدرهای وابسته.">
        <x-slot:actions><x-button href="{{ route('admin.sections.create') }}" size="sm" icon="ki-filled ki-plus">افزودن بخش</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>پس‌زمینه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">نام بخش</x-datatable-heading>
            <x-datatable-heading sortable sort="slug" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه متنی</x-datatable-heading>
            <x-datatable-heading>تصاویر</x-datatable-heading>
            <x-datatable-heading>آیتم‌ها</x-datatable-heading>
            <x-datatable-heading>اسلایدرها</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($sections as $section)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $section->id }}</td>
                <td class="px-5 py-3">
                    @if ($section->bg_image)
                        <img src="{{ Storage::disk('public')->url($section->bg_image) }}" alt="{{ $section->name }}" class="size-10 rounded-md border border-gray-200 object-cover" />
                    @elseif ($section->bg_color)
                        <span class="inline-block size-8 rounded-md border border-gray-200" style="background-color: {{ $section->bg_color }}"></span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $section->name }}</td>
                <td class="px-5 py-3 font-mono text-gray-600">{{ $section->slug }}</td>
                <td class="px-5 py-3">{{ $section->images_count }}</td>
                <td class="px-5 py-3">{{ $section->items_count }}</td>
                <td class="px-5 py-3">{{ $section->sliders_count }}</td>
                <td class="px-5 py-3"><x-badge :variant="$section->status->color()" size="sm" dot>{{ $section->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($section->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end"><x-button href="{{ route('admin.sections.edit', $section->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button></td>
            </tr>
        @empty
            <tr><td colspan="10" class="px-5 py-12 text-center text-gray-400">بخشی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$sections" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
