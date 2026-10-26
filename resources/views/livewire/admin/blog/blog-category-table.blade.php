<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در دسته‌بندی‌ها" placeholder="نام یا شناسهٔ متنی..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="دسته‌بندی‌های وبلاگ" description="این دسته‌بندی‌ها مستقل از دسته‌بندی‌های کالاهای فروشگاه هستند.">
        <x-slot:actions><x-button href="{{ route('admin.admin-blog.categories.create') }}" size="sm" icon="ki-filled ki-plus">افزودن دسته‌بندی</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">عنوان و شناسه متنی</x-datatable-heading>
            <x-datatable-heading>والد</x-datatable-heading>
            <x-datatable-heading>نوشته‌ها</x-datatable-heading>
            <x-datatable-heading sortable sort="priority" :sort-by="$sortBy" :sort-dir="$sortDir">اولویت</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading class="w-20 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($categories as $category)
            @php
                $hasPosts = $category->posts_count > 0;
                $hasChildren = $category->children_count > 0;
                $deleteMessage = $hasPosts
                    ? "این دسته‌بندی {$category->posts_count} نوشته دارد و قابل حذف نیست. برای حفظ نوشته‌ها، ابتدا آن‌ها را به دسته دیگری منتقل کنید. " . ($hasChildren ? "این دسته همچنین {$category->children_count} زیر‌دسته دارد که پس از حذف دستهٔ والد به دستهٔ اصلی تبدیل می‌شوند." : '')
                    : ($hasChildren
                        ? "این دسته‌بندی {$category->children_count} زیر‌دسته دارد. با حذف دسته، زیر‌دسته‌ها باقی می‌مانند و به دسته‌های اصلی تبدیل می‌شوند. ادامه می‌دهید؟"
                        : "دسته‌بندی «{$category->name}» حذف شود؟");
            @endphp
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $category->id }}</td>
                <td class="px-5 py-3">
                    @if ($category->image)
                        <span class="blog-manage-table-thumb blog-manage-table-thumb--category"><img src="{{ public_image_url($category->image) }}" alt="" loading="lazy" /></span>
                    @else <span class="blog-manage-table-thumb blog-manage-table-thumb--category"><i class="ki-filled ki-picture" aria-hidden="true"></i></span> @endif
                </td>
                <td class="px-5 py-3"><a href="{{ route('admin.admin-blog.categories.edit', $category->id) }}" class="blog-manage-table-title hover:text-primary">{{ $category->name }}</a><span class="blog-manage-table-subtitle" dir="ltr">{{ $category->slug }}</span></td>
                <td class="px-5 py-3 text-gray-600">{{ $category->parent?->name ?? 'دستهٔ اصلی' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $category->posts_count }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $category->priority }}</td>
                <td class="px-5 py-3"><x-badge :variant="$category->status->color()" size="sm" dot>{{ $category->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات دسته‌بندی ' . $category->name">
                        <div class="menu-item"><a href="{{ route('admin.admin-blog.categories.edit', $category->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span><span class="menu-title">ویرایش</span></a></div>
                        <div class="menu-item">
                            <form method="POST" action="{{ route('admin.admin-blog.categories.destroy', $category->id) }}" data-confirm-open="blog-delete-confirm" data-confirm-title="{{ $hasPosts ? 'امکان حذف دسته‌بندی نیست' : 'حذف دسته‌بندی' }}" data-confirm-message="{{ $deleteMessage }}" data-confirm-text="حذف دسته‌بندی" data-confirm-block="{{ $hasPosts ? 'true' : 'false' }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="menu-link w-full text-danger" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-trash" aria-hidden="true"></i></span><span class="menu-title">حذف دسته‌بندی</span></button>
                            </form>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center"><div class="blog-manage-empty"><i class="ki-filled ki-category" aria-hidden="true"></i><strong>دسته‌بندی‌ای پیدا نشد</strong><span>{{ $this->activeFilterCount > 0 ? 'عبارت جستجو یا فیلتر وضعیت را تغییر دهید.' : 'برای شروع، اولین دسته‌بندی وبلاگ را ایجاد کنید.' }}</span></div></td></tr>
        @endforelse
    </x-datatable>
    <x-confirm-dialog id="blog-delete-confirm" variant="danger" confirm-text="حذف" />
    <x-pagination-bar :paginator="$categories" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
