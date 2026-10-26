<x-panel-layout title="کدهای تخفیف" sub-title="ساخت و مدیریت پیشنهادهای خرید و محدودیت استفاده از آن‌ها.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.discounts.discount-table />
</x-panel-layout>
