<div>
    <x-admin.filter-panel>
        <x-admin.filter-search label="جستجو در آیتم‌ها" placeholder="عنوان آیتم..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable :title="$kindTitle">
        <x-slot:actions><x-button :href="route('admin.footer.create', $kind)" size="sm" icon="ki-filled ki-plus">افزودن آیتم</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه</x-datatable-heading>
            <x-datatable-heading sortable :sort="$kind === 'features' || $kind === 'groups' ? 'title' : 'label'" :sort-by="$sortBy" :sort-dir="$sortDir">عنوان</x-datatable-heading>
            <x-datatable-heading>مقصد</x-datatable-heading>
            <x-datatable-heading sortable sort="sort_order" :sort-by="$sortBy" :sort-dir="$sortDir">ترتیب</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($entries as $entry)
            <tr>
                <td class="px-5 py-3">{{ $entry->id }}</td>
                <td class="px-5 py-3 font-medium"><div class="flex items-center gap-2">@if ($entry->image_path ?? null)<img src="{{ public_image_url($entry->image_path) }}" alt="" class="h-9 w-9 rounded-lg object-contain" />@endif {{ $entry->title ?? $entry->label }}</div></td>
                <td class="max-w-64 truncate px-5 py-3 text-gray-500" dir="ltr">{{ $entry->url ?? '—' }}</td>
                <td class="px-5 py-3">{{ $entry->sort_order }}</td>
                <td class="px-5 py-3"><x-badge :variant="$entry->status->color()" size="sm" dot>{{ $entry->status->trans() }}</x-badge></td>
                <td class="px-5 py-3"><x-table.actions :label="'عملیات '.($entry->title ?? $entry->label)">
                    <div class="menu-item"><a class="menu-link" role="menuitem" href="{{ route('admin.footer.edit', [$kind, $entry->id]) }}"><span class="menu-icon"><i class="ki-filled ki-notepad-edit"></i></span><span class="menu-title">ویرایش</span></a></div>
                    <div class="menu-item"><form method="POST" action="{{ route('admin.footer.destroy', [$kind, $entry->id]) }}" onsubmit="return confirm('این آیتم حذف شود؟')">@csrf @method('DELETE')<button class="menu-link w-full text-danger" type="submit" role="menuitem"><span class="menu-icon"><i class="ki-filled ki-trash"></i></span><span class="menu-title">حذف</span></button></form></div>
                </x-table.actions></td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">آیتمی برای نمایش وجود ندارد.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$entries" :per-page="$perPage" :per-page-options="[10, 15, 25, 50]" />
</div>
