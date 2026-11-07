<x-panel-layout title="{{ $item->name }}" sub-title="جزئیات آیتم ثبت‌شده در فروشگاه شما">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    <div class="flex justify-end gap-2">
        <x-button href="{{ route('vendor.products.create', ['item_id' => $item->id]) }}" icon="ki-filled ki-plus">افزودن محصول</x-button>
        <x-button href="{{ route('vendor.items.index') }}" variant="light">بازگشت</x-button>
    </div>
    <x-card title="مشخصات آیتم">
        <dl class="grid gap-4 sm:grid-cols-2">
            <div><dt class="text-gray-500">نام</dt><dd class="font-medium">{{ $item->name }}</dd></div>
            <div><dt class="text-gray-500">دسته‌بندی</dt><dd>{{ $item->category->name }}</dd></div>
            <div><dt class="text-gray-500">کد</dt><dd dir="ltr">{{ $item->code }}</dd></div>
            <div><dt class="text-gray-500">وضعیت</dt><dd><x-badge :variant="$item->status->color()">{{ $item->status->trans() }}</x-badge></dd></div>
        </dl>
        @if ($item->image) <img class="mt-5 h-32 w-32 rounded-lg object-cover" src="{{ public_image_url($item->image) }}" alt="تصویر {{ $item->name }}"> @endif
        @if ($item->description) <p class="mt-5 whitespace-pre-line">{{ $item->description }}</p> @endif
    </x-card>
    <x-datatable title="محصولات این آیتم" description="تنوع‌های ثبت‌شده">
        <x-slot:head>
            <x-datatable-heading>شناسه</x-datatable-heading><x-datatable-heading>رنگ و سایز</x-datatable-heading>
            <x-datatable-heading>قیمت</x-datatable-heading><x-datatable-heading>موجودی</x-datatable-heading>
            <x-datatable-heading>وضعیت بررسی</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($item->products as $product)
            <tr class="border-b"><td class="px-5 py-3">{{ $product->id }}</td>
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
            <tr><td colspan="6" class="px-5 py-8 text-center text-gray-500">هنوز محصولی برای این آیتم ثبت نشده است.</td></tr>
        @endforelse
    </x-datatable>
</x-panel-layout>
