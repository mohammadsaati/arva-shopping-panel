<x-panel-layout title="بخش‌های فروشگاه" sub-title="مدیریت بخش‌های صفحه فروشگاه، تصاویر و ظاهر هر بخش.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.sections.section-table />
</x-panel-layout>
