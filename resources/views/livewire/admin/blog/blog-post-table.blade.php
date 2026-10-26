<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در نوشته‌ها" placeholder="عنوان یا شناسه متنی..." />
        <x-admin.filter-select label="دسته‌بندی" empty-label="همه دسته‌بندی‌ها" wire:model.live="categoryId">
            @foreach ($categories as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="نوشته‌های وبلاگ" description="نوشته‌ها، وضعیت انتشار و انتخاب مطالب شاخص صفحهٔ وبلاگ.">
        <x-slot:actions><x-button href="{{ route('admin.admin-blog.posts.create') }}" size="sm" icon="ki-filled ki-plus">نوشتهٔ جدید</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>تصویر</x-datatable-heading>
            <x-datatable-heading sortable sort="title" :sort-by="$sortBy" :sort-dir="$sortDir">نوشته</x-datatable-heading>
            <x-datatable-heading>دسته‌بندی</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="is_featured" :sort-by="$sortBy" :sort-dir="$sortDir">مطالب شاخص</x-datatable-heading>
            <x-datatable-heading sortable sort="published_at" :sort-by="$sortBy" :sort-dir="$sortDir">انتشار</x-datatable-heading>
            <x-datatable-heading class="w-20 text-end">عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($posts as $post)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $post->id }}</td>
                <td class="px-5 py-3">
                    @if ($post->featured_image)<img src="{{ public_image_url($post->featured_image) }}" alt="{{ $post->title }}" class="h-12 w-20 rounded-md border border-gray-200 object-cover" loading="lazy" />@else<span class="text-gray-400">—</span>@endif
                </td>
                <td class="px-5 py-3"><span class="font-medium text-gray-900">{{ $post->title }}</span><span class="mt-0.5 block text-xs text-gray-400">{{ $post->slug }}</span></td>
                <td class="px-5 py-3 text-gray-600">{{ $post->category->name }}</td>
                <td class="px-5 py-3"><x-badge :variant="$post->status->color()" size="sm" dot>{{ $post->status->trans() }}</x-badge></td>
                <td class="px-5 py-3">@if ($post->is_featured)<x-badge variant="primary" size="sm">{{ $post->featured_order + 1 }}</x-badge>@else<span class="text-gray-400">—</span>@endif</td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($post->published_at) ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات نوشتهٔ ' . $post->title">
                        <div class="menu-item"><a href="{{ route('admin.admin-blog.posts.show', $post->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-eye" aria-hidden="true"></i></span><span class="menu-title">مشاهده</span></a></div>
                        <div class="menu-item"><a href="{{ route('admin.admin-blog.posts.edit', $post->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span><span class="menu-title">ویرایش</span></a></div>
                        <div class="menu-item">
                            <form method="POST" action="{{ route('admin.admin-blog.posts.destroy', $post->id) }}" data-confirm-open="blog-delete-confirm" data-confirm-title="حذف نوشته" data-confirm-message="نوشتهٔ «{{ $post->title }}» و تصویر شاخص آن حذف شود؟" data-confirm-text="حذف نوشته">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="menu-link w-full text-danger" role="menuitem" data-dropdown-dismiss="true"><span class="menu-icon"><i class="ki-filled ki-trash" aria-hidden="true"></i></span><span class="menu-title">حذف نوشته</span></button>
                            </form>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">نوشته‌ای با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>
    <x-confirm-dialog id="blog-delete-confirm" variant="danger" confirm-text="حذف" />
    <x-pagination-bar :paginator="$posts" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
