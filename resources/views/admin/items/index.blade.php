<x-panel-layout
    title="آیتم‌ها"
    sub-title="مدیریت اطلاعات اصلی کالاها و دسته‌بندی آن‌ها."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.items.item-table />
</x-panel-layout>
