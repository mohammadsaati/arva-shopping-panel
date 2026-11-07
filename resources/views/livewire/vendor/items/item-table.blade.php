<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در آیتم‌ها" placeholder="نام آیتم..." />
    </x-admin.filter-panel>
    <x-datatable title="آیتم‌های من" description="آیتم‌های ثبت‌شده در فروشگاه شما">
        <x-slot:actions><x-button href="{{ route('vendor.items.create') }}" size="sm" icon="ki-filled ki-plus">افزودن آیتم</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">نام آیتم</x-datatable-heading>
            <x-datatable-heading>دسته‌بندی</x-datatable-heading><x-datatable-heading>محصولات</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($items as $item)
            <tr class="border-b"><td class="px-5 py-3">{{ $item->id }}</td>
                <td class="px-5 py-3 font-medium">{{ $item->name }}</td>
                <td class="px-5 py-3">{{ $item->category->name }}</td>
                <td class="px-5 py-3">{{ $item->products_count }}</td>
                <td class="px-5 py-3">{{ jalali_date($item->created_at) ?? '—' }}</td>
                <td class="px-5 py-3"><a class="text-primary hover:underline" href="{{ route('vendor.items.show', $item->id) }}">مشاهده</a></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">آیتمی ثبت نشده است.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$items" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
