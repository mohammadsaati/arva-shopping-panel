<x-user.account-shell title="نشانی‌های من" sub-title="نشانی‌های تحویل سفارش را ثبت و مدیریت کنید." active="addresses">
    <x-slot:actions>
        <x-button href="{{ route('account.addresses.create') }}" icon="ki-filled ki-plus">
            افزودن نشانی
        </x-button>
    </x-slot:actions>

    @if (session('success'))
        <div class="user-account-alert user-account-alert--success" role="status">{{ session('success') }}</div>
    @endif

    <livewire:storefront.account.address-table />
</x-user.account-shell>
