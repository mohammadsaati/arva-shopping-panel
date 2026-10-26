<x-panel-layout
    title="جزئیات استان"
    sub-title="مشخصات استان و تعداد شهرهای متصل به آن."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <x-button href="{{ route('admin.provinces.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست استان‌ها
        </x-button>
        <x-button href="{{ route('admin.provinces.create') }}" icon="ki-filled ki-plus">
            افزودن استان
        </x-button>
        <x-button href="{{ route('admin.provinces.edit', $province->id) }}" variant="light" icon="ki-filled ki-notepad-edit">
            ویرایش استان
        </x-button>
    </div>

    <x-card title="اطلاعات استان">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">نام استان</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $province->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تعداد شهرها</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $province->cities_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">وضعیت</dt>
                <dd class="mt-1">
                    <x-badge :variant="$province->status->color()" size="sm" dot>{{ $province->status->trans() }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تاریخ ثبت</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ jalali_date($province->created_at) ?? '—' }}</dd>
            </div>
        </dl>
    </x-card>
</x-panel-layout>
