<x-panel-layout
    title="دسته‌بندی‌ها"
    sub-title="مدیریت دسته‌بندی‌های کالا و تصویر نمایشی آن‌ها."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.categories.category-table />
</x-panel-layout>
