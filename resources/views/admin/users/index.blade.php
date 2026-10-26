<x-panel-layout
    title="کاربران"
    sub-title="فهرست حساب‌های کاربری ثبت‌شده در فروشگاه."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    <livewire:admin.users.user-table />
</x-panel-layout>
