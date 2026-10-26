<div class="sm:col-span-2">
    <input type="hidden" name="item_ids_present" value="1" />
    @foreach ($selectedItemIds as $itemId)
        <input type="hidden" name="item_ids[]" value="{{ $itemId }}" wire:key="section-item-value-{{ $itemId }}" />
    @endforeach

    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h4 class="font-medium text-gray-900">آیتم‌های این بخش</h4>
            <p class="mt-1 text-xs text-gray-500">آیتم‌ها را جستجو کنید و همهٔ آیتم‌های قابل‌نمایش این صفحه را یک‌جا انتخاب کنید؛ خود بخش نیز باید فعال باشد.</p>
        </div>
        <span class="badge badge-primary" aria-live="polite">{{ count($selectedItemIds) }} از ۱۰۰ آیتم انتخاب شده</span>
    </div>

    <div class="card mb-3">
        <x-admin.filter-panel
            embedded
            class="card-body"
            title="جستجو و محدود کردن فهرست"
            :active-count="$this->activeFilterCount"
        >
            <x-slot:actions>
                <button
                    type="button"
                    wire:click="toggleSelectedOnly"
                    aria-pressed="{{ $selectedOnly ? 'true' : 'false' }}"
                    class="btn btn-sm {{ $selectedOnly ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-1.5"
                >
                    <i class="ki-filled ki-check-circle" aria-hidden="true"></i>
                    فقط انتخاب‌شده‌ها
                </button>
                <button
                    type="button"
                    wire:click="selectVisibleItems"
                    wire:loading.attr="disabled"
                    wire:target="selectVisibleItems,clearVisibleItems"
                    @disabled($homeAvailability === 'unavailable')
                    class="btn btn-sm btn-primary inline-flex items-center gap-1.5"
                >
                    <i class="ki-filled ki-check-circle" aria-hidden="true"></i>
                    انتخاب همهٔ این صفحه
                </button>
                <button
                    type="button"
                    wire:click="clearVisibleItems"
                    wire:loading.attr="disabled"
                    wire:target="selectVisibleItems,clearVisibleItems"
                    class="btn btn-sm btn-light inline-flex items-center gap-1.5"
                >
                    <i class="ki-filled ki-cross" aria-hidden="true"></i>
                    پاک‌کردن انتخاب‌های این صفحه
                </button>
                <span wire:loading wire:target="search,homeAvailability,toggleSelectedOnly,toggleItem,selectVisibleItems,clearVisibleItems,perPage,previousPage,nextPage" class="text-xs text-gray-500" role="status">در حال بارگذاری...</span>
            </x-slot:actions>

            <x-admin.filter-search label="جستجو در آیتم‌ها" placeholder="نام، شناسه یا کد آیتم..." />
            <x-admin.filter-select label="نمایش در صفحه اصلی" empty-label="همه آیتم‌ها" wire:model.live="homeAvailability">
                <option value="available">قابل نمایش در صفحه اصلی</option>
                <option value="unavailable">غیرقابل نمایش در صفحه اصلی</option>
            </x-admin.filter-select>
        </x-admin.filter-panel>
    </div>

    <x-datatable title="انتخاب آیتم‌ها" description="آیتم‌های فعال و آیتم‌هایی که قبلاً برای این بخش انتخاب شده‌اند.">
        <x-slot:head>
            <x-datatable-heading class="w-24">انتخاب</x-datatable-heading>
            <x-datatable-heading>آیتم</x-datatable-heading>
            <x-datatable-heading>کد</x-datatable-heading>
            <x-datatable-heading>دسته‌بندی</x-datatable-heading>
            <x-datatable-heading>وضعیت</x-datatable-heading>
            <x-datatable-heading>نمایش در صفحه اصلی</x-datatable-heading>
        </x-slot:head>

        @forelse ($items as $item)
            @php($isSelected = in_array($item->id, $selectedItemIds, true))
            <tr wire:key="section-item-row-{{ $item->id }}" class="border-b border-gray-100 transition-colors hover:bg-primary/5 {{ $isSelected ? 'bg-primary/5' : '' }}">
                <td class="px-5 py-3">
                    <button
                        type="button"
                        wire:click="toggleItem({{ $item->id }})"
                        wire:loading.attr="disabled"
                        wire:target="toggleItem"
                        aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                        aria-label="{{ $isSelected ? 'حذف' : 'انتخاب' }} آیتم {{ $item->name }}"
                        class="btn btn-sm {{ $isSelected ? 'btn-primary' : 'btn-light' }} inline-flex items-center gap-1"
                    >
                        <i class="ki-filled {{ $isSelected ? 'ki-check' : 'ki-plus' }}" aria-hidden="true"></i>
                        {{ $isSelected ? 'انتخاب شده' : 'انتخاب' }}
                    </button>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ public_image_url($item->image) }}" alt="" class="size-10 shrink-0 rounded-md border border-gray-200 object-cover" loading="lazy" />
                        <div>
                            <span class="font-medium text-gray-900">{{ $item->name }}</span>
                            <span class="block text-xs text-gray-400">{{ $item->slug }}</span>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3 font-mono text-gray-600">{{ $item->code }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $item->category?->name ?? '—' }}</td>
                <td class="px-5 py-3"><x-badge :variant="$item->status->color()" size="sm" dot>{{ $item->status->trans() }}</x-badge></td>
                <td class="px-5 py-3">
                    <x-badge :variant="$itemHomeAvailability[$item->id]['available'] ? 'success' : 'danger'" size="sm" dot>
                        {{ $itemHomeAvailability[$item->id]['available'] ? 'قابل نمایش' : 'غیرقابل نمایش' }}
                    </x-badge>
                    <p class="mt-1 text-xs text-gray-500">{{ $itemHomeAvailability[$item->id]['reason'] }}</p>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-10 text-center text-gray-500">آیتمی با این فیلترها پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$items" :per-page="$perPage" :per-page-options="[10, 15, 25, 50]" />

    @if ($selectionError)
        <p class="mt-2 text-xs text-danger" role="alert">{{ $selectionError }}</p>
    @endif
    @if ($errors->has('item_ids') || $errors->has('item_ids.*'))
        <p class="mt-2 text-xs text-danger" role="alert">{{ $errors->first('item_ids') ?: $errors->first('item_ids.*') }}</p>
    @endif
</div>
