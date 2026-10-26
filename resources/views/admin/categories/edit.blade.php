<x-panel-layout
    title="ویرایش دسته‌بندی"
    sub-title="مشخصات دسته‌بندی را ویرایش کنید؛ برای جایگزینی تصویر، فایل جدید انتخاب کنید."
>
    <div class="mb-5 flex justify-end">
        <x-button href="{{ route('admin.categories.specifications.index', $category->id) }}" variant="light" icon="ki-filled ki-setting-2">
            مدیریت مشخصات کالا
        </x-button>
    </div>
    <x-card.form
        action="{{ route('admin.categories.update', $category->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        id="edit_category_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.categories.show', $category->id)]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="ساختار دسته‌بندی فروشگاه" description="نام و جایگاه دسته را مشخص کنید؛ ظاهر و وضعیت در کارت‌های کنار فرم قرار دارند." icon="ki-filled ki-category" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="عنوان دسته و نشانی متنی آن" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام دسته‌بندی" :value="$category->name" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" :value="$category->slug" dir="ltr" required />
                </x-admin.form-section>
                <x-admin.form-section title="جایگاه در فروشگاه" description="دستهٔ والد و ترتیب نمایش" icon="ki-filled ki-abstract-26" :two-columns="true">
                    <x-form.select
                        name="parent_id"
                        label="دسته‌بندی والد"
                        :options="$parents"
                        :selected="$category->parent_id"
                        placeholder="بدون والد"
                    />
                    <x-form.input name="priority" label="اولویت نمایش" type="number" min="0" :value="$category->priority" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="ظاهر دسته" description="آیکن و تصویر مناسب دسته‌بندی" icon="ki-filled ki-picture">
                    <x-form.input name="icon" label="آیکن (اختیاری)" :value="$category->icon" placeholder="برای نمونه: ki-filled ki-category" dir="ltr" />
                    <x-form.file
                        name="image"
                        label="تصویر دسته‌بندی"
                        accept="image/*"
                        :current-image="$category->image ? Storage::disk('public')->url($category->image) : null"
                        hint="برای نگه‌داشتن تصویر فعلی، فایل جدید انتخاب نکنید. حداکثر حجم ۵ مگابایت است."
                        class="sm:col-span-2"
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت دسته‌بندی" icon="ki-filled ki-eye">
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="$category->status->value"
                        required
                    />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
