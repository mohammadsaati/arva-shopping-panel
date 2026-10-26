<x-panel-layout
    title="استان‌ها"
    sub-title="مدیریت استان‌های مورد استفاده در اطلاعات شهر و نشانی مشتریان."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.provinces.province-table />
</x-panel-layout>
