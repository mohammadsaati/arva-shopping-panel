<x-panel-layout
    title="افزودن برند"
    sub-title="اطلاعات برند را وارد کنید و در صورت نیاز، تصویر نشان آن را بارگذاری کنید."
>
    <x-card.form
        action="{{ route('admin.brands.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_brand_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.brands.index')]"
        :submit="['title' => 'ایجاد برند', 'type' => 'primary']"
    >
        <x-admin.form-intro title="برند فروشگاه" description="نام و شناسهٔ برند را ثبت کنید؛ تصویر و وضعیت نمایش جداگانه تنظیم می‌شوند." icon="ki-filled ki-tag" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="اطلاعاتی که در فروشگاه و نشانی برند دیده می‌شوند." icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام برند" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="نشان برند" description="تصویر برند را برای نمایش کنار محصولات انتخاب کنید." icon="ki-filled ki-picture">
                    <x-form.file
                        name="image"
                        label="تصویر برند"
                        accept="image/*"
                        hint="اختیاری؛ حداکثر حجم فایل ۵ مگابایت است و تصویر به‌صورت محلی ذخیره می‌شود."
                        class="sm:col-span-2"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت برند در فروشگاه" icon="ki-filled ki-eye">
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value"
                        required
                    />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
