<x-panel-layout
    title="محصولات"
    sub-title="مدیریت قیمت، موجودی و تنوع‌های هر آیتم."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.products.product-table />
</x-panel-layout>
