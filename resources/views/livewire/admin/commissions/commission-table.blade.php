<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در کمیسیون‌ها" placeholder="عنوان کمیسیون..." />
    </x-admin.filter-panel>

    <x-datatable title="کمیسیون‌ها" description="عنوان و درصدهای قابل انتخاب برای فروشندگان">
        <x-slot:actions>
            <x-button href="{{ route('admin.commissions.create') }}" size="sm" icon="ki-filled ki-plus">افزودن کمیسیون</x-button>
        </x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="title" :sort-by="$sortBy" :sort-dir="$sortDir">عنوان</x-datatable-heading>
            <x-datatable-heading sortable sort="rate_bps" :sort-by="$sortBy" :sort-dir="$sortDir">درصد</x-datatable-heading>
            <x-datatable-heading>تعداد فروشندگان</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($commissions as $commission)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $commission->id }}</td>
                <td class="px-5 py-3 font-medium text-gray-900">{{ $commission->title }}</td>
                <td class="px-5 py-3 text-gray-700" dir="ltr">{{ number_format($commission->rate_bps / 100, 2) }}٪</td>
                <td class="px-5 py-3 text-gray-600">{{ $commission->vendors_count }}</td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($commission->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <a href="{{ route('admin.commissions.edit', $commission->id) }}" class="text-primary hover:underline">ویرایش</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">کمیسیونی تعریف نشده است.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$commissions" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
