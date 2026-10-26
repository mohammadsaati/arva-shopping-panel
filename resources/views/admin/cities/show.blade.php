<x-panel-layout
    title="جزئیات شهر"
    sub-title="مشخصات شهر و استان ثبت‌شده برای آن."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <x-button href="{{ route('admin.cities.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست شهرها
        </x-button>
        <x-button href="{{ route('admin.cities.create') }}" icon="ki-filled ki-plus">
            افزودن شهر
        </x-button>
        <x-button href="{{ route('admin.cities.edit', $city->id) }}" variant="light" icon="ki-filled ki-notepad-edit">
            ویرایش شهر
        </x-button>
    </div>

    <x-card title="اطلاعات شهر">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">نام شهر</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $city->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">شناسه متنی</dt>
                <dd class="mt-1 font-mono text-gray-900">{{ $city->slug }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">استان</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $city->province->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">وضعیت</dt>
                <dd class="mt-1">
                    <x-badge :variant="$city->status->color()" size="sm" dot>{{ $city->status->trans() }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تاریخ ثبت</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ jalali_date($city->created_at) ?? '—' }}</dd>
            </div>
        </dl>
    </x-card>
</x-panel-layout>
