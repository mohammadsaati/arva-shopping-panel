<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در کدهای تخفیف" placeholder="جستجو در نام یا کد..." />
        <x-admin.filter-select label="نوع تخفیف" empty-label="همه انواع" wire:model.live="type">
            @foreach ($typeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست کدهای تخفیف" description="میزان مصرف، محدوده اعتبار و وضعیت هر کد را مدیریت کنید.">
        <x-slot:actions>
            <x-button href="{{ route('admin.discounts.create') }}" size="sm" icon="ki-filled ki-plus">افزودن کد تخفیف</x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="code" :sort-by="$sortBy" :sort-dir="$sortDir">کد / نام</x-datatable-heading>
            <x-datatable-heading>کاربر</x-datatable-heading>
            <x-datatable-heading sortable sort="type" :sort-by="$sortBy" :sort-dir="$sortDir">نوع و مقدار</x-datatable-heading>
            <x-datatable-heading sortable sort="usage_count" :sort-by="$sortBy" :sort-dir="$sortDir">استفاده</x-datatable-heading>
            <x-datatable-heading sortable sort="ends_at" :sort-by="$sortBy" :sort-dir="$sortDir">اعتبار تا</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($discounts as $discount)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $discount->id }}</td>
                <td class="px-5 py-3">
                    <span class="block font-mono font-semibold text-gray-900" dir="ltr">{{ $discount->code }}</span>
                    <span class="block text-xs text-gray-500">{{ $discount->name }}</span>
                    @if ($discount->products_count > 0 || $discount->categories_count > 0)
                        <span class="mt-1 block text-xs text-gray-400">محدود به {{ $discount->products_count }} محصول و {{ $discount->categories_count }} دسته‌بندی</span>
                    @endif
                </td>
                <td class="px-5 py-3 whitespace-nowrap text-gray-600">
                    @if ($discount->user)
                        <span>{{ $discount->user->first_name }} {{ $discount->user->last_name }}</span>
                        <span class="block font-mono text-xs text-gray-400" dir="ltr">{{ $discount->user->phone }}</span>
                    @else
                        <span class="text-gray-400">همه کاربران</span>
                    @endif
                </td>
                <td class="px-5 py-3 whitespace-nowrap">
                    <x-badge :variant="$discount->type->color()" size="sm">{{ $discount->type->trans() }}</x-badge>
                    <span class="ms-1 font-medium">{{ number_format($discount->amount) }}{{ $discount->type === \App\Enums\DiscountTypeEnum::PERCENTAGE ? '٪' : ' تومان' }}</span>
                </td>
                <td class="px-5 py-3 whitespace-nowrap text-gray-600">
                    {{ number_format($discount->usage_count) }}
                    <span class="text-gray-400">/ {{ $discount->usage_limit !== null ? number_format($discount->usage_limit) : '∞' }}</span>
                </td>
                <td class="px-5 py-3 whitespace-nowrap text-gray-500">{{ $discount->ends_at ? jalali_date($discount->ends_at, 'Y/m/d H:i') : 'بدون انقضا' }}</td>
                <td class="px-5 py-3"><x-badge :variant="$discount->status->color()" size="sm" dot>{{ $discount->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-end">
                    <x-button href="{{ route('admin.discounts.edit', $discount->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit">ویرایش</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">کد تخفیفی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$discounts" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
