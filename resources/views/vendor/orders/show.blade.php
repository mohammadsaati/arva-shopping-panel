<x-panel-layout title="سفارش #{{ $vendorOrder->order_id }}" sub-title="اقلام و مبلغ مربوط به فروشگاه شما">
    <div class="flex justify-end"><x-button href="{{ route('vendor.orders.index') }}" variant="light">بازگشت به سفارش‌ها</x-button></div>
    <div class="grid gap-5 lg:grid-cols-2">
        <x-card title="اطلاعات سفارش">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-gray-500">وضعیت</dt><dd><x-badge :variant="$vendorOrder->status->color()">{{ $vendorOrder->status->trans() }}</x-badge></dd></div>
                <div><dt class="text-gray-500">تاریخ</dt><dd>{{ jalali_date($vendorOrder->created_at, 'Y/m/d H:i') ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">مشتری</dt><dd>{{ $vendorOrder->order->user->first_name }} {{ $vendorOrder->order->user->last_name }}</dd></div>
                <div><dt class="text-gray-500">گیرنده</dt><dd>{{ $vendorOrder->order->recipient_name ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">شماره تماس</dt><dd dir="ltr">{{ $vendorOrder->order->phone_number ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">نشانی</dt><dd>{{ $vendorOrder->order->full_address ?: '—' }}</dd></div>
            </dl>
        </x-card>
        <x-card title="مبالغ فروشنده">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-gray-500">جمع کالاها</dt><dd>{{ number_format($vendorOrder->subtotal_amount) }} تومان</dd></div>
                <div><dt class="text-gray-500">تخفیف</dt><dd>{{ number_format($vendorOrder->discount_amount) }} تومان</dd></div>
                <div><dt class="text-gray-500">کمیسیون</dt><dd>{{ number_format($vendorOrder->commission_amount) }} تومان</dd></div>
                <div><dt class="text-gray-500">سهم فروشنده</dt><dd class="font-semibold">{{ number_format($vendorOrder->payable_amount) }} تومان</dd></div>
            </dl>
        </x-card>
    </div>
    <x-datatable title="اقلام سفارش" description="فقط محصولات فروشگاه شما">
        <x-slot:head>
            <x-datatable-heading>محصول</x-datatable-heading><x-datatable-heading>تعداد</x-datatable-heading>
            <x-datatable-heading>قیمت واحد</x-datatable-heading><x-datatable-heading>مبلغ</x-datatable-heading>
            <x-datatable-heading>کمیسیون</x-datatable-heading>
        </x-slot:head>
        @foreach ($vendorOrder->items as $item)
            <tr class="border-b"><td class="px-5 py-3">{{ $item->product->item->name }}</td>
                <td class="px-5 py-3">{{ $item->count }}</td>
                <td class="px-5 py-3">{{ number_format($item->unit_price) }} تومان</td>
                <td class="px-5 py-3">{{ number_format($item->total_price) }} تومان</td>
                <td class="px-5 py-3">{{ number_format($item->commission_amount) }} تومان</td>
            </tr>
        @endforeach
    </x-datatable>
</x-panel-layout>
