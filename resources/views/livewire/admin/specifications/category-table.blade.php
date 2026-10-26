<div>
    <x-admin.filter-panel>
        <x-admin.filter-search label="جستجو در دسته‌بندی‌ها" placeholder="نام یا شناسه دسته‌بندی..." />
    </x-admin.filter-panel>

    <x-datatable title="دسته‌بندی‌ها" description="مشخصات هر دسته‌بندی به آیتم‌های همان دسته‌بندی تعلق می‌گیرد.">
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">دسته‌بندی</x-datatable-heading>
            <x-datatable-heading>والد</x-datatable-heading>
            <x-datatable-heading>گروه‌ها</x-datatable-heading>
            <x-datatable-heading>مشخصه‌ها</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($categories as $category)
            <tr class="border-b border-gray-100 hover:bg-primary/5">
                <td class="px-5 py-3">{{ $category->id }}</td>
                <td class="px-5 py-3">
                    <span class="font-medium">{{ $category->name }}</span>
                    <small class="block text-gray-500" dir="ltr">{{ $category->slug }}</small>
                </td>
                <td class="px-5 py-3">{{ $category->parent?->name ?? '—' }}</td>
                <td class="px-5 py-3">{{ $category->specification_groups_count }}</td>
                <td class="px-5 py-3">{{ $category->specifications_count }}</td>
                <td class="px-5 py-3">{{ jalali_date($category->created_at) ?? '—' }}</td>
                <td class="px-5 py-3">
                    <a class="text-primary hover:underline" href="{{ route('admin.categories.specifications.index', $category->id) }}">مدیریت مشخصات</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-5 py-10 text-center text-gray-500">دسته‌بندی‌ای پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$categories" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
