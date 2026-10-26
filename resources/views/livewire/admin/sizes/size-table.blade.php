<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در سایزها" placeholder="مقدار سایز..." />
    </x-admin.filter-panel>

    <x-datatable title="فهرست سایزها" description="سایزهای قابل انتخاب برای محصولات فروشگاه.">
        <x-slot:actions>
            <x-button href="{{ route('admin.sizes.create') }}" size="sm" icon="ki-filled ki-plus">افزودن سایز</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="value" :sort-by="$sortBy" :sort-dir="$sortDir">مقدار سایز</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($sizes as $size)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $size->id }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $size->value }}</td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($size->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-button href="{{ route('admin.sizes.edit', $size->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-400">سایزی ثبت نشده یا موردی با این جستجو پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$sizes" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
