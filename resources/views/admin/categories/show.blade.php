<x-panel-layout
    title="جزئیات دسته‌بندی"
    sub-title="مشخصات دسته‌بندی و ارتباط آن با سایر دسته‌بندی‌ها و کالاها."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <x-button href="{{ route('admin.categories.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست دسته‌بندی‌ها
        </x-button>
        <x-button href="{{ route('admin.categories.create') }}" icon="ki-filled ki-plus">
            افزودن دسته‌بندی
        </x-button>
        <x-button href="{{ route('admin.categories.edit', $category->id) }}" variant="light" icon="ki-filled ki-notepad-edit">
            ویرایش دسته‌بندی
        </x-button>
        <x-button href="{{ route('admin.categories.specifications.index', $category->id) }}" variant="light" icon="ki-filled ki-setting-2">
            مدیریت مشخصات کالا
        </x-button>
    </div>

    <x-card title="اطلاعات دسته‌بندی">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">نام دسته‌بندی</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $category->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">شناسه متنی</dt>
                <dd class="mt-1 font-mono text-gray-900">{{ $category->slug }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">دسته‌بندی والد</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $category->parent?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">اولویت نمایش</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $category->priority }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">آیکن</dt>
                <dd class="mt-1 font-mono text-gray-900">{{ $category->icon ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تعداد زیر‌دسته‌ها</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $category->children_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تعداد آیتم‌ها</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $category->items_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">گروه‌ها / مشخصه‌های کالا</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $category->specification_groups_count }} / {{ $category->specifications_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">وضعیت</dt>
                <dd class="mt-1">
                    <x-badge :variant="$category->status->color()" size="sm" dot>{{ $category->status->trans() }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تاریخ ثبت</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ jalali_date($category->created_at) ?? '—' }}</dd>
            </div>
            @if ($category->image)
                <div>
                    <dt class="text-sm text-gray-500">تصویر</dt>
                    <dd class="mt-2">
                        <img
                            src="{{ Storage::disk('public')->url($category->image) }}"
                            alt="{{ $category->name }}"
                            class="size-28 rounded-lg border border-gray-200 object-cover"
                        />
                    </dd>
                </div>
            @endif
        </dl>
    </x-card>
</x-panel-layout>
