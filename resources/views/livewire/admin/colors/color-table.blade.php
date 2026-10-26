<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در رنگ‌ها" placeholder="نام یا مقدار رنگ..." />
    </x-admin.filter-panel>

    <x-datatable title="فهرست رنگ‌ها" description="رنگ‌های قابل انتخاب برای محصولات فروشگاه.">
        <x-slot:actions>
            <x-button href="{{ route('admin.colors.create') }}" size="sm" icon="ki-filled ki-plus">افزودن رنگ</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">نام رنگ</x-datatable-heading>
            <x-datatable-heading sortable sort="value" :sort-by="$sortBy" :sort-dir="$sortDir">مقدار</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($colors as $color)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $color->id }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $color->name }}</td>
                <td class="px-5 py-3 font-mono text-gray-600" dir="ltr">
                    @if (preg_match('/^#[0-9A-Fa-f]{6}$/', $color->value))
                        <span class="me-2 inline-block size-4 rounded-full border border-gray-300 align-middle" style="background-color: {{ $color->value }}" aria-hidden="true"></span>
                    @endif
                    {{ $color->value }}
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($color->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-button href="{{ route('admin.colors.edit', $color->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400">رنگی ثبت نشده یا موردی با این جستجو پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$colors" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
