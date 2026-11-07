<x-panel-layout title="محصول #{{ $product->id }}" sub-title="{{ $product->item->name }}">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    <div class="flex justify-end"><x-button href="{{ route('vendor.products.index') }}" variant="light">بازگشت به محصولات</x-button></div>
    @php($vendorOffer = $product->vendorOffersForManagement->first())
    <x-card title="مشخصات محصول">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div><dt class="text-gray-500">آیتم</dt><dd><a class="text-primary" href="{{ route('vendor.items.show', $product->item_id) }}">{{ $product->item->name }}</a></dd></div>
            <div><dt class="text-gray-500">قیمت</dt><dd>{{ number_format($product->price) }} تومان</dd></div>
            <div><dt class="text-gray-500">موجودی</dt><dd>{{ $product->stock }}</dd></div>
            <div><dt class="text-gray-500">هزینه ارسال</dt><dd>{{ number_format($product->shipping_price) }} تومان</dd></div>
            <div><dt class="text-gray-500">رنگ</dt><dd>{{ $product->color?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">سایز</dt><dd>{{ $product->size?->value ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">وضعیت</dt><dd><x-badge :variant="$product->status->color()">{{ $product->status->trans() }}</x-badge></dd></div>
            @if ($vendorOffer)
                <div><dt class="text-gray-500">وضعیت بررسی ادمین</dt><dd><x-badge :variant="$vendorOffer->approval_status->color()" dot>{{ $vendorOffer->approval_status->trans() }}</x-badge></dd></div>
            @endif
        </dl>
        @if ($vendorOffer?->rejection_reason)
            <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light p-4">
                <h3 class="text-sm font-semibold text-danger">دلیل رد محصول</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $vendorOffer->rejection_reason }}</p>
            </div>
        @endif
        @if ($product->images->isNotEmpty())
            <div class="mt-5 flex flex-wrap gap-3">
                @foreach ($product->images as $image)
                    <img class="h-24 w-24 rounded-lg object-cover" src="{{ public_image_url($image->image) }}" alt="تصویر {{ $product->item->name }}">
                @endforeach
            </div>
        @endif
    </x-card>
</x-panel-layout>
