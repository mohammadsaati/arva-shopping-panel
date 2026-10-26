<x-panel-layout title="داشبورد" sub-title="مرکز فرمان سبک برای مدیریت سریع، بدون آمار سنگین روی دیتابیس.">
    <x-slot:actions>
        <x-button href="{{ route('admin.orders.index') }}" size="sm" icon="ki-filled ki-basket">سفارش‌ها</x-button>
        <x-button href="{{ route('admin.items.create') }}" variant="light" size="sm" icon="ki-filled ki-plus">آیتم جدید</x-button>
    </x-slot:actions>
    <livewire:admin.dashboard.overview />
</x-panel-layout>
