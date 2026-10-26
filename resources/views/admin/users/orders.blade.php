<x-panel-layout
    :title="'سفارش‌های ' . $user->first_name . ' ' . $user->last_name"
    sub-title="سفارش‌های ثبت‌شده توسط این کاربر را مشاهده و مدیریت کنید."
>
    <div class="flex justify-end">
        <x-button href="{{ route('admin.users.show', $user->id) }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به جزئیات کاربر
        </x-button>
    </div>

    <livewire:admin.orders.order-table :user-id="$user->id" />
</x-panel-layout>
