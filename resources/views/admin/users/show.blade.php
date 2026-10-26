<x-panel-layout
    title="جزئیات کاربر"
    sub-title="اطلاعات حساب کاربری و پروفایل"
>
    <div class="flex justify-end gap-2">
        <x-button href="{{ route('admin.users.edit', $user->id) }}" variant="light" icon="ki-filled ki-notepad-edit">
            ویرایش کاربر
        </x-button>
        <x-button href="{{ route('admin.users.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست کاربران
        </x-button>
    </div>

    <x-card title="اطلاعات حساب">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">نام و نام خانوادگی</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">شماره تلفن</dt>
                <dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $user->phone }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">نوع حساب</dt>
                <dd class="mt-1">
                    <x-badge variant="info" size="sm">{{ $user->type->trans() }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">وضعیت</dt>
                <dd class="mt-1">
                    <x-badge :variant="$user->status->color()" size="sm" dot>
                       {{ $user->status->trans() }}
                    </x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تاریخ عضویت</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ jalali_date($user->created_at) ?? '—' }}</dd>
            </div>
        </dl>
    </x-card>

    <x-card title="اطلاعات پروفایل">
        @if ($user->profile)
            <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <dt class="text-sm text-gray-500">شماره کارت بانکی</dt>
                    <dd class="mt-1 font-medium text-gray-900" dir="ltr">{{ $user->profile->bank_card ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">شغل</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ $user->profile->job ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">شناسه شهر</dt>
                    <dd class="mt-1 font-medium text-gray-900">{{ $user->profile->city_id ?: '—' }}</dd>
                </div>
            </dl>
        @else
            <p class="text-sm text-gray-500">برای این کاربر هنوز پروفایلی ثبت نشده است.</p>
        @endif
    </x-card>
</x-panel-layout>
