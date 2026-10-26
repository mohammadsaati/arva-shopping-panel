<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در دسته‌بندی‌ها" placeholder="نام یا شناسه..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="دسته‌بندی‌ها" description="فهرست دسته‌بندی‌های فروشگاه و ارتباط آن‌ها با آیتم‌ها.">
        <x-slot:actions>
            <x-button href="{{ route('admin.categories.create') }}" size="sm" icon="ki-filled ki-plus">افزودن دسته‌بندی</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">دسته‌بندی</x-datatable-heading>
            <x-datatable-heading>والد</x-datatable-heading>
            <x-datatable-heading>آیتم‌ها / زیر‌دسته‌ها</x-datatable-heading>
            <x-datatable-heading sortable sort="priority" :sort-by="$sortBy" :sort-dir="$sortDir">اولویت</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-16 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($categories as $category)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $category->id }}</td>
                <td class="px-5 py-3">
                    @if ($category->image)
                        <img
                            src="{{ Storage::disk('public')->url($category->image) }}"
                            alt="{{ $category->name }}"
                            class="size-10 rounded-md border border-gray-200 object-cover"
                        />
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <span class="font-medium text-gray-900">{{ $category->name }}</span>
                    <span class="mt-0.5 block text-xs text-gray-400">{{ $category->slug }}</span>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $category->parent?->name ?? '—' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $category->items_count }} / {{ $category->children_count }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $category->priority }}</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$category->status->color()" size="sm" dot>{{ $category->status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($category->created_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات دسته‌بندی ' . $category->name">
                        <div class="menu-item">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">ویرایش</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.categories.show', $category->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-eye" aria-hidden="true"></i></span>
                                <span class="menu-title">مشاهده</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.categories.specifications.index', $category->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-setting-2" aria-hidden="true"></i></span>
                                <span class="menu-title">مدیریت مشخصات</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="px-5 py-12 text-center text-gray-400">دسته‌بندی‌ای با این مشخصات پیدا نشد.</td></tr>
        @endforelse

    </x-datatable>

    <x-pagination-bar :paginator="$categories" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
