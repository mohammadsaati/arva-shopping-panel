<x-panel-layout
    title="افزودن آیتم"
    sub-title="اطلاعات آیتم را ثبت کنید و در صورت نیاز، اولین محصول آن را همین‌جا اضافه کنید."
>
    <div class="mb-5 flex items-center justify-end">
        <x-button href="{{ route('admin.items.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست آیتم‌ها
        </x-button>
    </div>

    <x-alert type="info" class="mb-5">
        فیلدهای مشخصات با انتخاب دسته‌بندی نمایش داده می‌شوند.
        <a href="{{ route('admin.specifications.index') }}" class="font-semibold underline">مدیریت فیلدهای دسته‌بندی‌ها</a>
    </x-alert>

    <x-card.form
        action="{{ route('admin.items.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_item_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.items.index')]"
        :submit="['title' => 'ایجاد آیتم', 'type' => 'primary']"
    >
        <x-admin.form-intro title="اطلاعات آیتم فروشگاه" description="ابتدا هویت و دستهٔ کالا را ثبت کنید؛ تصویر، وضعیت و پیشنهاد شگفت‌انگیز در ستون کناری قرار دارند." icon="ki-filled ki-package" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="نام، نشانی متنی و کد یکتای آیتم" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام آیتم" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" dir="ltr" required />
                    <x-form.input name="code" label="کد آیتم" type="number" min="1" dir="ltr" required />
                </x-admin.form-section>
                <x-admin.form-section title="دسته و برند" description="جایگاه این آیتم در کاتالوگ فروشگاه" icon="ki-filled ki-category" :two-columns="true">
                    <x-form.select2
                        name="category_id"
                        label="دسته‌بندی"
                        :route="route('admin.categories.select-options')"
                        placeholder="انتخاب دسته‌بندی"
                        required
                    />
                    <x-form.select
                        name="brand_id"
                        label="برند"
                        :options="$brands"
                        placeholder="بدون برند"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="توضیحات" description="متنی که کنار مشخصات کالا نمایش داده می‌شود." icon="ki-filled ki-notepad-edit">
                    <x-form.textarea name="description" label="توضیحات" rows="4" class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات آیتم">
                <x-admin.form-section title="تصویر آیتم" description="تصویر اصلی کالا در فهرست و صفحهٔ آیتم" icon="ki-filled ki-picture">
                    <x-form.file
                        name="image"
                        label="تصویر آیتم"
                        accept="image/*"
                        hint="اختیاری؛ حداکثر حجم فایل ۵ مگابایت است و تصویر به‌صورت محلی ذخیره می‌شود."
                        class="sm:col-span-2"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت آیتم در فروشگاه" icon="ki-filled ki-eye">
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value"
                        required
                    />
                </x-admin.form-section>
                <x-admin.form-section title="پیشنهاد شگفت‌انگیز" description="در صورت فعال‌سازی، زمان پایان را مشخص کنید." icon="ki-filled ki-discount">
                    <x-form.toggle
                        name="is_amazing"
                        label="پیشنهاد شگفت‌انگیز"
                        :checked="(bool) old('is_amazing', false)"
                        hint="این آیتم در پیشنهادهای شگفت‌انگیز نمایش داده شود."
                    />
                    <x-date-picket
                        name="amazing_end_time"
                        label="زمان پایان پیشنهاد"
                        :value="old('amazing_end_time', '')"
                        placeholder="انتخاب تاریخ و ساعت"
                    />
                </x-admin.form-section>
            </aside>
        </div>

        <x-admin.form-section title="مشخصات کالا" description="فیلدهای این بخش با انتخاب دسته‌بندی نمایش داده می‌شوند." icon="ki-filled ki-setting-2">
        <section
            data-item-specifications
            data-current-category="{{ old('category_id', '') }}"
            data-url-template="{{ route('admin.items.specification-fields', ['categoryId' => '__CATEGORY__']) }}"
            class="grid gap-5"
            aria-label="مشخصات کالا"
        >
            @include('admin.items.partials.specification-fields', ['item' => null])
        </section>
        </x-admin.form-section>

        <section class="admin-form-section">
            <div class="mb-4">
                <h4 class="text-sm font-semibold text-gray-900">محصول اولیه (اختیاری)</h4>
                <p class="mt-1 text-xs text-gray-500">قیمت را وارد کنید تا محصول همزمان با آیتم ساخته شود؛ بعداً هم می‌توانید تنوع‌های بیشتری اضافه کنید.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <x-form.input name="product[price]" label="قیمت (تومان)" type="number" min="0" dir="ltr" />
                <x-form.select name="product[color_id]" label="رنگ" :options="$colors" placeholder="بدون رنگ" />
                <x-form.select name="product[size_id]" label="سایز" :options="$sizes" placeholder="بدون سایز" />
                <x-form.input name="product[stock]" label="موجودی" type="number" min="0" dir="ltr" />
                <x-form.input name="product[shipping_price]" label="هزینه ارسال پیش‌فرض (تومان)" type="number" min="0" dir="ltr" />
                <x-form.select
                    name="product[status]"
                    label="وضعیت محصول"
                    :options="$statusOptions"
                    :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value"
                />
            </div>
            <div class="mt-5">
                <x-admin.product-city-rates :city-options="$cities" />
            </div>
        </section>
    </x-card.form>
</x-panel-layout>
