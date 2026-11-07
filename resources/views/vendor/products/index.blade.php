<x-panel-layout title="محصولات من" sub-title="محصولات ثبت‌شده برای آیتم‌های شما.">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    <livewire:vendor.products.product-table />
</x-panel-layout>
