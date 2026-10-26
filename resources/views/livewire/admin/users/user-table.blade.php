<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در کاربران" placeholder="جستجو در نام یا شماره تلفن..." />
        <x-admin.filter-select label="وضعیت حساب" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
        <x-admin.filter-select label="نوع حساب" empty-label="همه انواع حساب" wire:model.live="type">
            @foreach ($typeOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="کاربران" description="حساب‌های کاربری ثبت‌شده در فروشگاه">
        <x-slot:actions>
            <x-button href="{{ route('admin.users.create') }}" size="sm" icon="ki-filled ki-plus">
                افزودن کاربر
            </x-button>
        </x-slot:actions>

        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="first_name" :sort-by="$sortBy" :sort-dir="$sortDir">نام و نام خانوادگی</x-datatable-heading>
            <x-datatable-heading sortable sort="phone" :sort-by="$sortBy" :sort-dir="$sortDir">شماره تلفن</x-datatable-heading>
            <x-datatable-heading sortable sort="type" :sort-by="$sortBy" :sort-dir="$sortDir">نوع حساب</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ عضویت</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse ($users as $user)
            @php
                $statusLabel = $statusOptions[$user->status->value];
                $typeLabel = $typeOptions[$user->type->value];
            @endphp
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">{{ $user->id }}</td>
                <td class="px-5 py-3">
                    <span class="font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</span>
                </td>
                <td class="px-5 py-3 text-gray-600">
                    <span dir="ltr">{{ $user->phone }}</span>
                </td>
                <td class="px-5 py-3">
                    <x-badge variant="info" size="sm">{{ $typeLabel }}</x-badge>
                </td>
                <td class="px-5 py-3">
                    <x-badge :variant="$user->status->color()" size="sm" dot>
                        {{ $statusLabel }}
                    </x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">
                    {{ jalali_date($user->created_at) ?? '—' }}
                </td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات کاربر ' . $user->first_name . ' ' . $user->last_name">
                        <div class="menu-item">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">ویرایش</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-eye" aria-hidden="true"></i></span>
                                <span class="menu-title">مشاهده</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a href="{{ route('admin.users.orders', $user->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-handcart" aria-hidden="true"></i></span>
                                <span class="menu-title">سفارش‌های کاربر</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                    <div class="flex flex-col items-center gap-2">
                        <i class="ki-filled ki-people text-4xl opacity-40"></i>
                        <p class="text-sm">کاربری با این مشخصات پیدا نشد.</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$users" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
