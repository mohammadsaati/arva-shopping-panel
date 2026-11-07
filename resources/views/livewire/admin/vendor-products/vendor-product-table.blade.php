<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در محصولات فروشندگان" placeholder="نام محصول، فروشگاه یا شماره تماس..." />
        <x-admin.filter-select label="وضعیت بررسی" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="محصولات فروشندگان" description="محصولات در انتظار تا زمان تأیید در فروشگاه نمایش داده نمی‌شوند.">
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>محصول</x-datatable-heading>
            <x-datatable-heading>فروشنده</x-datatable-heading>
            <x-datatable-heading sortable sort="price" :sort-by="$sortBy" :sort-dir="$sortDir">قیمت</x-datatable-heading>
            <x-datatable-heading sortable sort="stock" :sort-by="$sortBy" :sort-dir="$sortDir">موجودی</x-datatable-heading>
            <x-datatable-heading sortable sort="approval_status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($vendorProducts as $vendorProduct)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">#{{ $vendorProduct->id }}</td>
                <td class="px-5 py-3">
                    <a href="{{ route('admin.vendor-products.show', $vendorProduct->id) }}" class="font-medium text-primary hover:underline">{{ $vendorProduct->product->item->name }}</a>
                    <span class="mt-0.5 block text-xs text-gray-400">{{ $vendorProduct->product->color?->name ?? 'بدون رنگ' }} / {{ $vendorProduct->product->size?->value ?? 'بدون سایز' }}</span>
                </td>
                <td class="px-5 py-3 text-gray-700">
                    {{ $vendorProduct->vendor->store_name ?: $vendorProduct->vendor->company_name }}
                    <span class="mt-0.5 block text-xs text-gray-400">{{ $vendorProduct->vendor->user->phone }}</span>
                </td>
                <td class="px-5 py-3 text-gray-700">{{ number_format($vendorProduct->effectivePrice($vendorProduct->product)) }} تومان</td>
                <td class="px-5 py-3 text-gray-700">{{ number_format($vendorProduct->stock) }}</td>
                <td class="px-5 py-3"><x-badge :variant="$vendorProduct->approval_status->color()" size="sm" dot>{{ $vendorProduct->approval_status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($vendorProduct->created_at) }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات محصول فروشنده ' . $vendorProduct->id">
                        <div class="menu-item">
                            <a href="{{ route('admin.vendor-products.show', $vendorProduct->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-shield-tick" aria-hidden="true"></i></span>
                                <span class="menu-title">بررسی محصول</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">محصولی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$vendorProducts" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
