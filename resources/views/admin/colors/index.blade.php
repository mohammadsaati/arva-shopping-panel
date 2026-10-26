<x-panel-layout
    title="رنگ‌ها"
    sub-title="مدیریت رنگ‌هایی که برای تنوع محصولات انتخاب می‌شوند."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.colors.color-table />
</x-panel-layout>
