<div class="user-address-table">
    <x-datatable title="نشانی‌های ثبت‌شده" description="محل تحویل مناسب را هنگام ثبت سفارش انتخاب کنید.">
        <x-slot:head>
            <x-datatable-heading>نشانی تحویل</x-datatable-heading>
            <x-datatable-heading>مشخصات گیرنده</x-datatable-heading>
            <x-datatable-heading class="w-28 text-end">ویرایش</x-datatable-heading>
        </x-slot:head>

        @forelse ($addresses as $address)
            <tr class="user-address-table__row">
                <td data-label="نشانی تحویل">
                    <div class="user-address-table__location">
                        <span class="user-address-table__pin" aria-hidden="true"><i class="ki-filled ki-geolocation"></i></span>
                        <div>
                            <strong class="user-address-table__primary">{{ $address->province->name }}، {{ $address->city->name }}</strong>
                            <span class="user-address-table__address">{{ $address->full_address }}</span>
                        </div>
                    </div>
                </td>
                <td data-label="گیرنده">
                    <span class="user-address-table__primary">{{ $address->recipient_name ?: 'گیرنده ثبت نشده' }}</span>
                    <span class="user-address-table__secondary" dir="ltr">{{ $address->phone_number ?: 'شماره تماس ثبت نشده' }}</span>
                </td>
                <td class="user-address-table__actions" data-label="ویرایش">
                    <x-button
                        href="{{ route('account.addresses.edit', $address->id) }}"
                        variant="light"
                        size="sm"
                        icon="ki-filled ki-notepad-edit"
                    >
                        ویرایش
                    </x-button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="user-address-table__empty">
                    <span class="user-address-table__empty-icon" aria-hidden="true">
                        <i class="ki-filled ki-geolocation"></i>
                    </span>
                    <strong>هنوز نشانی‌ای ثبت نکرده‌اید</strong>
                    <span>برای ثبت سفارش، ابتدا یک نشانی برای تحویل کالا اضافه کنید.</span>
                    <x-button href="{{ route('account.addresses.create') }}" icon="ki-filled ki-plus">
                        افزودن اولین نشانی
                    </x-button>
                </td>
            </tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$addresses" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
