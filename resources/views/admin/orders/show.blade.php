<x-panel-layout title="جزئیات سفارش #{{ $order->id }}" sub-title="اطلاعات ثبت، تحویل و اقلام سفارش.">
    <div class="mb-5 flex justify-end">
        <x-button href="{{ route('admin.orders.index') }}" variant="light" icon="ki-filled ki-arrow-right">بازگشت به سفارش‌ها</x-button>
    </div>

    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 grid gap-5 lg:grid-cols-3">
        <x-card title="اطلاعات سفارش">
            <dl class="grid gap-4 text-sm">
                <div><dt class="text-gray-500">شماره سفارش</dt><dd class="mt-1 font-medium text-gray-900">#{{ $order->id }}</dd></div>
                <div><dt class="text-gray-500">تاریخ ثبت</dt><dd class="mt-1 font-medium text-gray-900">{{ jalali_date($order->created_at, 'Y/m/d H:i') ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">وضعیت سفارش</dt><dd class="mt-1"><x-badge :variant="$order->status->color()" size="sm" dot>{{ $order->status->trans() }}</x-badge></dd></div>
                <div><dt class="text-gray-500">وضعیت پرداخت</dt><dd class="mt-1"><x-badge :variant="$order->payment_status->color()" size="sm" dot>{{ $order->payment_status->trans() }}</x-badge></dd></div>
                @php($payment = $order->resolvedPaymentTransaction())
                <div><dt class="text-gray-500">درگاه</dt><dd class="mt-1 font-medium text-gray-900">{{ $payment?->paymentGateway?->name ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">Authority</dt><dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $payment?->authority ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">شماره پیگیری درگاه</dt><dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $payment?->transaction_id ?: '—' }}</dd></div>
                @if ($order->discount_code)
                    <div><dt class="text-gray-500">کد تخفیف</dt><dd class="mt-1 font-mono font-medium text-gray-900" dir="ltr">{{ $order->discount_code }}</dd></div>
                @endif
                <div><dt class="text-gray-500">تاریخ پرداخت</dt><dd class="mt-1 font-medium text-gray-900">{{ $order->paid_at ? jalali_date($order->paid_at, 'Y/m/d H:i') : '—' }}</dd></div>
                <div><dt class="text-gray-500">کد پیگیری</dt><dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $order->tracking_code ?: '—' }}</dd></div>
            </dl>
        </x-card>

        <x-card title="مشتری">
            <dl class="grid gap-4 text-sm">
                <div><dt class="text-gray-500">نام و نام خانوادگی</dt><dd class="mt-1 font-medium text-gray-900">{{ $order->user->first_name }} {{ $order->user->last_name }}</dd></div>
                <div><dt class="text-gray-500">شماره تلفن حساب</dt><dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $order->user->phone }}</dd></div>
            </dl>
            <div class="mt-5"><x-button href="{{ route('admin.users.show', $order->user_id) }}" variant="light" size="sm" icon="ki-filled ki-eye">مشاهده کاربر</x-button></div>
        </x-card>

        <x-card title="اطلاعات تحویل">
            <dl class="grid gap-4 text-sm">
                <div><dt class="text-gray-500">گیرنده</dt><dd class="mt-1 font-medium text-gray-900">{{ $order->recipient_name ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">شماره تماس گیرنده</dt><dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $order->phone_number ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">استان و شهر</dt><dd class="mt-1 font-medium text-gray-900">{{ $order->province ?: '—' }}، {{ $order->city ?: '—' }}</dd></div>
                <div><dt class="text-gray-500">نشانی کامل</dt><dd class="mt-1 font-medium leading-7 text-gray-900">{{ $order->full_address ?: '—' }}</dd></div>
            </dl>
        </x-card>
    </div>

    <div class="mb-5">
        <x-card.form
            id="order-status-form"
            :action="route('admin.orders.status.update', $order->id)"
            method="PATCH"
            title="تغییر وضعیت سفارش"
            :submit="['title' => 'ذخیره وضعیت', 'icon' => 'ki-filled ki-check']"
            data-confirm-open="order-status-confirm"
            data-confirm-title="تأیید تغییر وضعیت"
            data-confirm-message="وضعیت سفارش #{{ $order->id }} به «:selection» تغییر کند؟"
            data-confirm-text="تغییر وضعیت"
        >
            <x-form.select
                name="status"
                label="وضعیت سفارش"
                :options="$statusOptions"
                :selected="$order->status->value"
                required
            />
        </x-card.form>
        <x-confirm-dialog id="order-status-confirm" />
    </div>

    @if ($order->paymentTransactions->isNotEmpty())
        <x-datatable title="تراکنش‌های پرداخت" description="هر تلاش پرداخت برای این سفارش" class="mb-5">
            <x-slot:head>
                <x-datatable-heading>شناسه</x-datatable-heading>
                <x-datatable-heading>درگاه</x-datatable-heading>
                <x-datatable-heading>مبلغ</x-datatable-heading>
                <x-datatable-heading>وضعیت</x-datatable-heading>
                <x-datatable-heading>Authority</x-datatable-heading>
                <x-datatable-heading>پیگیری درگاه</x-datatable-heading>
                <x-datatable-heading>تاریخ</x-datatable-heading>
            </x-slot:head>
            @foreach ($order->paymentTransactions->sortByDesc('id') as $transaction)
                <tr class="border-b border-gray-100">
                    <td class="px-5 py-3 font-mono text-sm">#{{ $transaction->id }}</td>
                    <td class="px-5 py-3">{{ $transaction->paymentGateway?->name ?: '—' }}</td>
                    <td class="px-5 py-3 whitespace-nowrap">{{ number_format($transaction->amount) }} تومان</td>
                    <td class="px-5 py-3">
                        <x-badge :variant="$transaction->status->color()" size="sm" dot>{{ $transaction->status->trans() }}</x-badge>
                    </td>
                    <td class="px-5 py-3 font-mono text-xs" dir="ltr">{{ $transaction->authority ?: '—' }}</td>
                    <td class="px-5 py-3 font-mono text-xs" dir="ltr">{{ $transaction->transaction_id ?: '—' }}</td>
                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ jalali_date($transaction->created_at, 'Y/m/d H:i') ?? '—' }}</td>
                </tr>
            @endforeach
        </x-datatable>
    @endif

    <x-datatable title="اقلام سفارش" description="قیمت‌ها و تعداد ثبت‌شده هنگام سفارش">
        <x-slot:head>
            <x-datatable-heading>محصول</x-datatable-heading>
            <x-datatable-heading>رنگ و سایز</x-datatable-heading>
            <x-datatable-heading>قیمت واحد</x-datatable-heading>
            <x-datatable-heading>تعداد</x-datatable-heading>
            <x-datatable-heading>مبلغ ردیف</x-datatable-heading>
        </x-slot:head>
        @forelse ($order->items as $orderItem)
            <tr class="border-b border-gray-100">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $orderItem->product->item->name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $orderItem->product->color?->name ?? '—' }} / {{ $orderItem->product->size?->value ?? '—' }}</td>
                <td class="px-5 py-3 whitespace-nowrap">{{ number_format($orderItem->unit_price) }} تومان</td>
                <td class="px-5 py-3">{{ $orderItem->count }}</td>
                <td class="px-5 py-3 whitespace-nowrap font-medium">{{ number_format($orderItem->total_price) }} تومان</td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">کالایی برای این سفارش ثبت نشده است.</td></tr>
        @endforelse
    </x-datatable>

    <x-card title="خلاصه مبلغ" class="mt-5">
        <dl class="ms-auto grid w-full max-w-sm gap-3 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-gray-500">جمع کالاها</dt><dd class="font-medium text-gray-900">{{ number_format($order->itemsSubtotal()) }} تومان</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-gray-500">هزینه ارسال</dt><dd class="font-medium text-gray-900">{{ number_format($order->shipping_amount) }} تومان</dd></div>
            @if ($order->off_amount > 0)
                <div class="flex justify-between gap-4"><dt class="text-gray-500">تخفیف{{ $order->discount_code ? ' (' . $order->discount_code . ')' : '' }}</dt><dd class="font-medium text-success">−{{ number_format($order->off_amount) }} تومان</dd></div>
            @endif
            <div class="flex justify-between gap-4 border-t border-gray-200 pt-3 text-base font-semibold"><dt>مبلغ نهایی</dt><dd>{{ number_format($order->amount) }} تومان</dd></div>
        </dl>
    </x-card>
</x-panel-layout>
