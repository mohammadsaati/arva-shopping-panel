<x-panel-layout
    title="ویرایش آیتم"
    sub-title="اطلاعات آیتم را ویرایش کنید؛ برای جایگزینی تصویر، فایل جدید انتخاب کنید."
>
    <div class="mb-5 flex justify-end">
        <x-button href="{{ route('admin.categories.specifications.index', $item->category_id) }}" variant="light" icon="ki-filled ki-setting-2">
            مدیریت فیلدهای مشخصات
        </x-button>
    </div>
    <x-card.form
        action="{{ route('admin.items.update', $item->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        id="edit_item_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.items.show', $item->id)]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="اطلاعات آیتم فروشگاه" description="ابتدا هویت و دستهٔ کالا را ثبت کنید؛ تصویر، وضعیت و پیشنهاد شگفت‌انگیز در ستون کناری قرار دارند." icon="ki-filled ki-package" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="نام، نشانی متنی و کد یکتای آیتم" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام آیتم" :value="$item->name" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" :value="$item->slug" dir="ltr" required />
                    <x-form.input name="code" label="کد آیتم" type="number" min="1" :value="$item->code" dir="ltr" required />
                </x-admin.form-section>
                <x-admin.form-section title="دسته و برند" description="جایگاه این آیتم در کاتالوگ فروشگاه" icon="ki-filled ki-category" :two-columns="true">
                    <x-form.select2
                        name="category_id"
                        label="دسته‌بندی"
                        :selected="$item->category_id"
                        :route="route('admin.categories.select-options')"
                        placeholder="انتخاب دسته‌بندی"
                        required
                    />
                    <x-form.select
                        name="brand_id"
                        label="برند"
                        :options="$brands"
                        :selected="$item->brand_id"
                        placeholder="بدون برند"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="توضیحات" description="متنی که کنار مشخصات کالا نمایش داده می‌شود." icon="ki-filled ki-notepad-edit">
                    <x-form.textarea name="description" label="توضیحات" :value="$item->description" rows="4" class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات آیتم">
                <x-admin.form-section title="تصویر آیتم" description="تصویر اصلی کالا در فهرست و صفحهٔ آیتم" icon="ki-filled ki-picture">
                    <x-form.file
                        name="image"
                        label="تصویر آیتم"
                        accept="image/*"
                        :current-image="$item->image ? Storage::disk('public')->url($item->image) : null"
                        hint="برای نگه‌داشتن تصویر فعلی، فایل جدید انتخاب نکنید. حداکثر حجم فایل ۵ مگابایت است."
                        class="sm:col-span-2"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت آیتم در فروشگاه" icon="ki-filled ki-eye">
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="$item->status->value"
                        required
                    />
                </x-admin.form-section>
                <x-admin.form-section title="پیشنهاد شگفت‌انگیز" description="در صورت فعال‌سازی، زمان پایان را مشخص کنید." icon="ki-filled ki-discount">
                    <x-form.toggle
                        name="is_amazing"
                        label="پیشنهاد شگفت‌انگیز"
                        :checked="$item->is_amazing"
                        hint="این آیتم در پیشنهادهای شگفت‌انگیز نمایش داده شود."
                    />
                    <x-date-picket
                        name="amazing_end_time"
                        label="زمان پایان پیشنهاد"
                        :value="old('amazing_end_time', $item->amazing_end_time ? jalali_date($item->amazing_end_time, 'Y/m/d H:i:s') : '')"
                        placeholder="انتخاب تاریخ و ساعت"
                    />
                </x-admin.form-section>
            </aside>
        </div>

        <x-admin.form-section title="مشخصات کالا" description="فیلدهای این بخش با انتخاب دسته‌بندی نمایش داده می‌شوند." icon="ki-filled ki-setting-2">
        <section
            data-item-specifications
            data-current-category="{{ old('category_id', $item->category_id) }}"
            data-url-template="{{ route('admin.items.specification-fields', ['categoryId' => '__CATEGORY__']) }}"
            class="grid gap-5"
            aria-label="مشخصات کالا"
        >
            @include('admin.items.partials.specification-fields')
        </section>
        </x-admin.form-section>
    </x-card.form>
</x-panel-layout>
