<x-panel-layout title="آیتم‌های من" sub-title="آیتم‌هایی که شما ثبت کرده‌اید.">
    @if (session('success')) <x-alert type="success">{{ session('success') }}</x-alert> @endif
    <livewire:vendor.items.item-table />
</x-panel-layout>
