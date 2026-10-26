<x-panel-layout
    title="ایجاد دسته‌بندی"
    sub-title="اطلاعات دسته‌بندی را وارد کنید و در صورت نیاز تصویر آن را بارگذاری کنید."
>
    <x-card.form
        action="{{ route('admin.categories.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_category_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.categories.index')]"
        :submit="['title' => 'ایجاد دسته‌بندی', 'type' => 'primary']"
    >
        <x-admin.form-intro title="ساختار دسته‌بندی فروشگاه" description="نام و جایگاه دسته را مشخص کنید؛ ظاهر و وضعیت در کارت‌های کنار فرم قرار دارند." icon="ki-filled ki-category" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="عنوان دسته و نشانی متنی آن" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام دسته‌بندی" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" dir="ltr" required />
                </x-admin.form-section>
                <x-admin.form-section title="جایگاه در فروشگاه" description="دستهٔ والد و ترتیب نمایش" icon="ki-filled ki-abstract-26" :two-columns="true">
                    <x-form.select
                        name="parent_id"
                        label="دسته‌بندی والد"
                        :options="$parents"
                        placeholder="بدون والد"
                    />
                    <x-form.input name="priority" label="اولویت نمایش" type="number" min="0" value="0" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="ظاهر دسته" description="آیکن و تصویر مناسب دسته‌بندی" icon="ki-filled ki-picture">
                    <x-form.input name="icon" label="آیکن (اختیاری)" placeholder="برای نمونه: ki-filled ki-category" dir="ltr" />
                    <x-form.file
                        name="image"
                        label="تصویر دسته‌بندی"
                        accept="image/*"
                        hint="اختیاری؛ تصویر حداکثر ۵ مگابایت است و به‌صورت محلی ذخیره می‌شود."
                        class="sm:col-span-2"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت دسته‌بندی" icon="ki-filled ki-eye">
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
