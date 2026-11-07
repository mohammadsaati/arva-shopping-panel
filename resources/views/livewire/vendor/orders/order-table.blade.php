<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در سفارش‌ها" placeholder="شماره سفارش یا نام مشتری..." />
    </x-admin.filter-panel>
    <x-datatable title="سفارش‌های من" description="فقط سهم فروشگاه شما از سفارش‌ها">
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه</x-datatable-heading>
            <x-datatable-heading>شماره سفارش</x-datatable-heading><x-datatable-heading>مشتری</x-datatable-heading>
            <x-datatable-heading sortable sort="total_amount" :sort-by="$sortBy" :sort-dir="$sortDir">مبلغ کالاها</x-datatable-heading>
            <x-datatable-heading>سهم فروشنده</x-datatable-heading><x-datatable-heading>وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($orders as $order)
            <tr class="border-b"><td class="px-5 py-3">{{ $order->id }}</td>
                <td class="px-5 py-3">#{{ $order->order_id }}</td>
                <td class="px-5 py-3">{{ $order->order->user->first_name }} {{ $order->order->user->last_name }}</td>
                <td class="px-5 py-3">{{ number_format($order->total_amount) }} تومان</td>
                <td class="px-5 py-3">{{ number_format($order->payable_amount) }} تومان</td>
                <td class="px-5 py-3"><x-badge :variant="$order->status->color()">{{ $order->status->trans() }}</x-badge></td>
                <td class="px-5 py-3">{{ jalali_date($order->created_at) ?? '—' }}</td>
                <td class="px-5 py-3"><a class="text-primary hover:underline" href="{{ route('vendor.orders.show', $order->id) }}">مشاهده</a></td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-8 text-center text-gray-500">سفارشی ثبت نشده است.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$orders" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
