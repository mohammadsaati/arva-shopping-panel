<x-panel-layout title="اسلایدرها" sub-title="مدیریت اسلایدهای تصویری صفحه‌های فروشگاه.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.sliders.slider-table />
</x-panel-layout>
