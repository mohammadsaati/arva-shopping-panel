<x-panel-layout title="بنرها" sub-title="مدیریت بنرهای محصولات، دسته‌بندی‌ها و پیوندهای فروشگاه.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.banners.banner-table />
</x-panel-layout>
