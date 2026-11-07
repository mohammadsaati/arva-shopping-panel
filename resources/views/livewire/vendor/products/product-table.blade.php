<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در محصولات" placeholder="نام آیتم..." />
    </x-admin.filter-panel>
    <x-datatable title="محصولات من" description="محصولات مربوط به آیتم‌های شما">
        <x-slot:actions><x-button href="{{ route('vendor.products.create') }}" size="sm" icon="ki-filled ki-plus">افزودن محصول</x-button></x-slot:actions>
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه</x-datatable-heading>
            <x-datatable-heading>آیتم</x-datatable-heading><x-datatable-heading>رنگ و سایز</x-datatable-heading>
            <x-datatable-heading sortable sort="price" :sort-by="$sortBy" :sort-dir="$sortDir">قیمت</x-datatable-heading>
            <x-datatable-heading sortable sort="stock" :sort-by="$sortBy" :sort-dir="$sortDir">موجودی</x-datatable-heading>
            <x-datatable-heading>وضعیت بررسی</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($products as $product)
            <tr class="border-b"><td class="px-5 py-3">{{ $product->id }}</td>
                <td class="px-5 py-3 font-medium">{{ $product->item->name }}</td>
                <td class="px-5 py-3">{{ $product->color?->name ?? '—' }} / {{ $product->size?->value ?? '—' }}</td>
                <td class="px-5 py-3">{{ number_format($product->price) }} تومان</td>
                <td class="px-5 py-3">{{ $product->stock }}</td>
                @php($vendorOffer = $product->vendorOffersForManagement->first())
                <td class="px-5 py-3">
                    @if ($vendorOffer)
                        <x-badge :variant="$vendorOffer->approval_status->color()" size="sm" dot>{{ $vendorOffer->approval_status->trans() }}</x-badge>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-5 py-3"><a class="text-primary hover:underline" href="{{ route('vendor.products.show', $product->id) }}">مشاهده</a></td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-5 py-8 text-center text-gray-500">محصولی ثبت نشده است.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$products" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
