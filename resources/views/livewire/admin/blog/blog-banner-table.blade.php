<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در بنرها" placeholder="عنوان یا متن بالای بنر..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="بنرهای وبلاگ" description="مدیریت تصویر، پیوند و زمان نمایش بنرهای صفحهٔ اصلی وبلاگ.">
        <x-slot:actions><x-button href="{{ route('admin.admin-blog.banners.create') }}" size="sm" icon="ki-filled ki-plus">افزودن بنر</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="title" :sort-by="$sortBy" :sort-dir="$sortDir">بنر</x-datatable-heading>
            <x-datatable-heading>پیوند</x-datatable-heading>
            <x-datatable-heading sortable sort="sort_order" :sort-by="$sortBy" :sort-dir="$sortDir">ترتیب</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="starts_at" :sort-by="$sortBy" :sort-dir="$sortDir">بازهٔ نمایش</x-datatable-heading>
            <x-datatable-heading class="w-20 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($banners as $banner)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $banner->id }}</td>
                <td class="px-5 py-3"><span class="blog-manage-table-thumb"><img src="{{ public_image_url($banner->image) }}" alt="" loading="lazy" /></span></td>
                <td class="px-5 py-3"><a href="{{ route('admin.admin-blog.banners.edit', $banner->id) }}" class="blog-manage-table-title hover:text-primary">{{ $banner->title }}</a><span class="blog-manage-table-subtitle">{{ $banner->eyebrow ?? 'بدون متن بالای عنوان' }}</span></td>
                <td class="px-5 py-3 text-gray-600">@if($banner->target_url)<span class="blog-manage-table-url" dir="ltr" title="{{ $banner->target_url }}">{{ $banner->target_url }}</span>@else<span class="text-gray-400">بدون پیوند</span>@endif</td>
                <td class="px-5 py-3 text-gray-600">{{ $banner->sort_order }}</td>
                <td class="px-5 py-3"><x-badge :variant="$banner->status->color()" size="sm" dot>{{ $banner->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-gray-500">
                    @if ($banner->starts_at === null && $banner->ends_at === null)
                        <span class="text-gray-600">همیشه</span>
                    @else
                        <span class="block">از {{ jalali_date($banner->starts_at) ?? 'همین حالا' }}</span>
                        <span class="mt-0.5 block text-xs">تا {{ jalali_date($banner->ends_at) ?? 'بدون پایان' }}</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات بنر ' . $banner->title">
                        <div class="menu-item"><a href="{{ route('admin.admin-blog.banners.edit', $banner->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span><span class="menu-title">ویرایش</span></a></div>
                        <div class="menu-item">
                            <form method="POST" action="{{ route('admin.admin-blog.banners.destroy', $banner->id) }}" data-confirm-open="blog-delete-confirm" data-confirm-title="حذف بنر" data-confirm-message="بنر «{{ $banner->title }}» و تصویرهای آن حذف شود؟" data-confirm-text="حذف بنر">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="menu-link w-full text-danger" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-trash" aria-hidden="true"></i></span><span class="menu-title">حذف بنر</span></button>
                            </form>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center"><div class="blog-manage-empty"><i class="ki-filled ki-picture" aria-hidden="true"></i><strong>بنری پیدا نشد</strong><span>{{ $this->activeFilterCount > 0 ? 'عبارت جستجو یا فیلتر وضعیت را تغییر دهید.' : 'برای شروع، اولین بنر وبلاگ را ایجاد کنید.' }}</span></div></td></tr>
        @endforelse
    </x-datatable>
    <x-confirm-dialog id="blog-delete-confirm" variant="danger" confirm-text="حذف" />
    <x-pagination-bar :paginator="$banners" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
