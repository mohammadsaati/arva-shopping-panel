<x-panel-layout title="درگاه‌های پرداخت" sub-title="روش‌های پرداخت فروشگاه را ببینید، فعال کنید و تنظیمات هر درگاه را کامل کنید.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.payment-gateways.payment-gateway-table />
</x-panel-layout>
