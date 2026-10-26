<x-panel-layout
    title="برندها"
    sub-title="مدیریت برندهای قابل اتصال به آیتم‌های فروشگاه."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.brands.brand-table />
</x-panel-layout>
