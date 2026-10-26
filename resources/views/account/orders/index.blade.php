<x-user.account-shell title="سفارش‌های من" sub-title="وضعیت و جزئیات سفارش‌های خود را پیگیری کنید." active="orders">
    @error('payment')
        <div class="user-account-alert" role="alert">{{ $message }}</div>
    @enderror
    <livewire:storefront.account.order-table />
</x-user.account-shell>
