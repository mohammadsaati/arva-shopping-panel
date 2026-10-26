<div class="user-address-table user-orders">
    @php($hasActiveFilters = $search !== '' || $status !== '')

    <section class="user-orders__filters" aria-labelledby="user-orders-filter-title">
        <div class="user-orders__filters-heading">
            <div class="user-orders__filters-title">
                <span class="user-orders__filters-icon" aria-hidden="true"><i class="ki-filled ki-filter"></i></span>
                <div>
                    <h2 id="user-orders-filter-title">پیدا کردن سفارش</h2>
                    <p>شماره سفارش را جستجو کنید یا بر اساس وضعیت فیلتر کنید.</p>
                </div>
            </div>
            @if ($hasActiveFilters)
                <button type="button" class="user-orders__clear" wire:click="clearFilters" wire:loading.attr="disabled">
                    <i class="ki-filled ki-cross" aria-hidden="true"></i>
                    پاک کردن فیلترها
                </button>
            @endif
        </div>

        <div class="user-orders__search-wrap">
            <label class="sr-only" for="order-history-search">جستجو با شماره سفارش</label>
            <span class="user-orders__search-icon" aria-hidden="true"><i class="ki-filled ki-magnifier"></i></span>
            <input
                id="order-history-search"
                class="user-orders__search"
                type="search"
                inputmode="numeric"
                autocomplete="off"
                placeholder="شماره سفارش را وارد کنید"
                wire:model.live.debounce.350ms="search"
                aria-describedby="order-history-search-hint"
            >
            @if ($search !== '')
                <button type="button" class="user-orders__search-clear" wire:click="clearSearch" aria-label="پاک کردن شماره سفارش">
                    <i class="ki-filled ki-cross" aria-hidden="true"></i>
                </button>
            @endif
            <span id="order-history-search-hint" class="user-orders__search-hint">می‌توانید شماره را با یا بدون # وارد کنید.</span>
        </div>

        <div class="user-orders__status-filter">
            <span class="user-orders__status-label">وضعیت سفارش</span>
            <div class="user-orders__status-list" role="group" aria-label="فیلتر بر اساس وضعیت سفارش">
                <button
                    type="button"
                    @class(['user-orders__status-chip', 'is-active' => $status === ''])
                    wire:click="setStatus('')"
                    aria-pressed="{{ $status === '' ? 'true' : 'false' }}"
                >
                    <i class="ki-filled ki-basket" aria-hidden="true"></i>
                    همه سفارش‌ها
                </button>
                @foreach ($statusOptions as $statusOption)
                    <button
                        type="button"
                        @class([
                            'user-orders__status-chip',
                            'is-active' => $status === $statusOption['value'],
                        ])
                        wire:click="setStatus('{{ $statusOption['value'] }}')"
                        aria-pressed="{{ $status === $statusOption['value'] ? 'true' : 'false' }}"
                    >
                        <span class="user-orders__status-dot user-orders__status-dot--{{ $statusOption['color'] }}" aria-hidden="true"></span>
                        {{ $statusOption['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    </section>

    <x-datatable title="سفارش‌های ثبت‌شده" description="آخرین وضعیت و جزئیات سفارش‌های شما در این فهرست قرار دارد.">
        <x-slot:head>
            <x-datatable-heading>شماره سفارش</x-datatable-heading>
            <x-datatable-heading>تاریخ ثبت</x-datatable-heading>
            <x-datatable-heading>مبلغ</x-datatable-heading>
            <x-datatable-heading>وضعیت سفارش</x-datatable-heading>
            <x-datatable-heading>پرداخت</x-datatable-heading>
            <x-datatable-heading class="text-end">جزئیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($orders as $order)
            <tr class="user-address-table__row">
                <td data-label="شماره سفارش"><span class="user-orders__number">#{{ $order->id }}</span></td>
                <td data-label="تاریخ ثبت">{{ jalali_date($order->created_at, 'Y/m/d H:i') ?? '—' }}</td>
                <td data-label="مبلغ">{{ number_format($order->amount) }} تومان</td>
                <td data-label="وضعیت سفارش"><x-badge :variant="$order->status->color()" size="sm" dot>{{ $order->status->trans() }}</x-badge></td>
                <td data-label="پرداخت"><x-badge :variant="$order->payment_status->color()" size="sm" dot>{{ $order->payment_status->trans() }}</x-badge></td>
                <td data-label="جزئیات" class="user-address-table__actions">
                    <x-button href="{{ route('orders.show', $order->id) }}" variant="light" size="sm" icon="ki-filled ki-eye">مشاهده</x-button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="user-address-table__empty">
                    <span class="user-address-table__empty-icon"><i class="ki-filled {{ $hasActiveFilters ? 'ki-magnifier' : 'ki-basket' }}" aria-hidden="true"></i></span>
                    <strong>{{ $hasActiveFilters ? 'سفارشی با این فیلترها پیدا نشد' : 'هنوز سفارشی ثبت نکرده‌اید' }}</strong>
                    <span>{{ $hasActiveFilters ? 'شماره سفارش یا وضعیت انتخاب‌شده را بررسی کنید.' : 'بعد از ثبت خرید، سفارش‌های شما در این قسمت نمایش داده می‌شوند.' }}</span>
                    @if ($hasActiveFilters)
                        <button type="button" class="user-orders__empty-action" wire:click="clearFilters">نمایش همه سفارش‌ها</button>
                    @else
                        <x-button href="{{ route('shop.index') }}" variant="light" size="sm" icon="ki-filled ki-shop">رفتن به فروشگاه</x-button>
                    @endif
                </td>
            </tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$orders" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
