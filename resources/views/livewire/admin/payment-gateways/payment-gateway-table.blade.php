<div>
    @error('status')
        <x-alert type="danger" class="mb-5">{{ $message }}</x-alert>
    @enderror

    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در درگاه‌ها" placeholder="جستجو در نام یا نوع..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="فهرست درگاه‌ها" description="هر ردیف یک روش پرداخت است. تنظیمات را کامل کنید و بعد درگاه را فعال کنید.">
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="name" :sort-by="$sortBy" :sort-dir="$sortDir">نام</x-datatable-heading>
            <x-datatable-heading sortable sort="driver" :sort-by="$sortBy" :sort-dir="$sortDir">نوع</x-datatable-heading>
            <x-datatable-heading>تنظیمات</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($gateways as $gateway)
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $gateway->id }}</td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-3">
                        <img src="{{ public_image_url($gateway->displayImage()) }}" alt="" class="h-10 w-10 rounded-lg border border-gray-200 bg-white object-contain p-1">
                        <span class="font-medium text-gray-900">{{ $gateway->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3">{{ $gateway->driver->trans() }}</td>
                <td class="px-5 py-3 text-sm text-gray-600">
                    @if ($gateway->driver === \App\Enums\PaymentGatewayDriverEnum::ZARINPAL)
                        <span class="block" dir="ltr">{{ $gateway->merchantId() !== '' ? $gateway->merchantId() : 'کد پذیرنده ثبت نشده' }}</span>
                        <span class="block text-xs text-gray-400">{{ $gateway->usesSandbox() ? 'آزمایشی' : 'عملیاتی' }} · {{ $gateway->currency()->trans() }}</span>
                    @endif
                </td>
                <td class="px-5 py-3"><x-badge :variant="$gateway->status->color()" size="sm" dot>{{ $gateway->status->trans() }}</x-badge></td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات درگاه '.$gateway->name">
                        <div class="menu-item">
                            <a href="{{ route('admin.payment-gateways.edit', $gateway->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-setting-2" aria-hidden="true"></i></span>
                                <span class="menu-title">تنظیمات</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <button type="button" class="menu-link w-full" role="menuitem" data-dropdown-dismiss="true" wire:click="toggleStatus({{ $gateway->id }})">
                                <span class="menu-icon"><i class="ki-filled {{ $gateway->status === \App\Enums\GlobalStatusEnum::ACTIVE ? 'ki-cross-circle' : 'ki-check-circle' }}" aria-hidden="true"></i></span>
                                <span class="menu-title">{{ $gateway->status === \App\Enums\GlobalStatusEnum::ACTIVE ? 'غیرفعال کردن' : 'فعال کردن' }}</span>
                            </button>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400">درگاهی با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$gateways" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
