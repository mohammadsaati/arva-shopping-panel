<x-panel-layout
    title="جزئیات آیتم"
    sub-title="مشخصات کالا و تنوع‌های محصول وابسته به آن."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <x-button href="{{ route('admin.items.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست آیتم‌ها
        </x-button>
        <div class="flex flex-wrap gap-2">
            <x-button href="{{ route('admin.categories.specifications.index', $item->category_id) }}" variant="light" icon="ki-filled ki-setting-2">
                مدیریت فیلدهای مشخصات
            </x-button>
            <x-button href="{{ route('admin.items.edit', $item->id) }}" variant="light" icon="ki-filled ki-notepad-edit">
                ویرایش آیتم
            </x-button>
            <x-button href="{{ route('admin.products.index', ['item_id' => $item->id]) }}" variant="light" icon="ki-filled ki-eye">
                مشاهده محصولات ({{ $item->products_count }})
            </x-button>
            <x-button href="{{ route('admin.products.create', ['item_id' => $item->id]) }}" icon="ki-filled ki-plus">
                افزودن محصول / تنوع
            </x-button>
        </div>
    </div>

    <x-card title="اطلاعات آیتم">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">نام آیتم</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $item->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">کد</dt>
                <dd class="mt-1 font-mono text-gray-900">{{ $item->code }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">شناسه متنی</dt>
                <dd class="mt-1 font-mono text-gray-900">{{ $item->slug }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">دسته‌بندی</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $item->category?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">برند</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $item->brand?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تعداد محصولات / تنوع‌ها</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ $item->products_count }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">وضعیت</dt>
                <dd class="mt-1">
                    <x-badge :variant="$item->status->color()" size="sm" dot>{{ $item->status->trans() }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">پیشنهاد شگفت‌انگیز</dt>
                <dd class="mt-1">
                    <x-badge :variant="$item->is_amazing ? 'success' : 'default'" size="sm">
                        {{ $item->is_amazing ? 'فعال' : 'غیرفعال' }}
                    </x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">پایان پیشنهاد</dt>
                <dd class="mt-1 font-medium text-gray-900">
                    {{ $item->amazing_end_time ? jalali_date($item->amazing_end_time, 'Y/m/d H:i') : '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تاریخ ثبت</dt>
                <dd class="mt-1 font-medium text-gray-900">{{ jalali_date($item->created_at) ?? '—' }}</dd>
            </div>
            @if ($item->image)
                <div>
                    <dt class="text-sm text-gray-500">تصویر</dt>
                    <dd class="mt-2">
                        <img
                            src="{{ Storage::disk('public')->url($item->image) }}"
                            alt="{{ $item->name }}"
                            class="size-24 rounded-lg border border-gray-200 object-cover"
                        />
                    </dd>
                </div>
            @endif
        </dl>

        @if ($item->description)
            <div class="mt-6 border-t border-gray-100 pt-5">
                <h3 class="text-sm font-semibold text-gray-700">توضیحات</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-600">{{ $item->description }}</p>
            </div>
        @endif
    </x-card>

    @php($specificationRows = $item->specificationDisplayRows())
    @if ($specificationRows->isNotEmpty())
        @foreach ($specificationRows->groupBy(fn (array $row) => $row['specification']->group->id) as $groupRows)
            <x-datatable :title="$groupRows->first()['specification']->group->name" class="mt-5">
                <x-slot:head>
                    <x-datatable-heading>مشخصه</x-datatable-heading>
                    <x-datatable-heading>مقدار</x-datatable-heading>
                </x-slot:head>
                @foreach ($groupRows as $row)
                    <tr><td>{{ $row['specification']->name }}</td><td>{{ $row['value'] }}</td></tr>
                @endforeach
            </x-datatable>
        @endforeach
    @endif
</x-panel-layout>
