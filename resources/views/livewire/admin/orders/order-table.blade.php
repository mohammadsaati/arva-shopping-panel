<div>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    @error('orderStatus')
        <x-alert type="danger" class="mb-5">{{ $message }}</x-alert>
    @enderror

    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search
            label="جستجو در سفارش‌ها"
            placeholder="شماره سفارش، نام یا تلفن..."
        />
        <x-admin.filter-select label="وضعیت سفارش" empty-label="همه وضعیت‌های سفارش" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="وضعیت پرداخت" empty-label="همه وضعیت‌های پرداخت" wire:model.live="paymentStatus">
            @foreach ($paymentStatusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable
        :title="$userId ? 'سفارش‌های کاربر #' . $userId : 'فهرست سفارش‌ها'"
        description="وضعیت هر سفارش را تغییر دهید یا جزئیات آن را ببینید."
    >
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شماره</x-datatable-heading>
            <x-datatable-heading>مشتری</x-datatable-heading>
            <x-datatable-heading sortable sort="amount" :sort-by="$sortBy" :sort-dir="$sortDir">مبلغ کل</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت سفارش</x-datatable-heading>
            <x-datatable-heading sortable sort="payment_status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت پرداخت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($orders as $order)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono font-medium text-gray-900">#{{ $order->id }}</td>
                <td class="px-5 py-3">
                    <span class="block font-medium text-gray-900">{{ $order->user->first_name }} {{ $order->user->last_name }}</span>
                    <span class="block text-xs text-gray-500" dir="ltr">{{ $order->user->phone }}</span>
                </td>
                <td class="px-5 py-3 whitespace-nowrap">{{ number_format($order->amount) }} تومان</td>
                <td class="px-5 py-3">
                    <x-order.status-picker :order="$order" :options="$statusOptions" :wire-id="$this->getId()" />
                </td>
                <td class="px-5 py-3"><x-badge :variant="$order->payment_status->color()" size="sm" dot>{{ $order->payment_status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ jalali_date($order->created_at, 'Y/m/d H:i') ?? '—' }}</td>
                <td class="px-5 py-3 text-end">
                    <x-button href="{{ route('admin.orders.show', $order->id) }}" variant="light" size="sm" icon="ki-filled ki-eye">جزئیات</x-button>
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400">سفارشی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$orders" :per-page="$perPage" :per-page-options="$perPageOptions" />

    <x-confirm-dialog id="order-status-confirm" />
</div>
