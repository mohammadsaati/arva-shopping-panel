<x-panel-layout title="افزودن بخش فروشگاه" sub-title="عنوان، ظاهر و تصاویر بخش جدید را وارد کنید.">
    <x-card.form
        action="{{ route('admin.sections.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_section_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.sections.index')]"
        :submit="['title' => 'ایجاد بخش', 'type' => 'primary']"
    >
        <x-admin.form-intro title="بخش فروشگاه" description="نام و آیتم‌های بخش را ثبت کنید؛ رنگ، تصاویر و وضعیت نمایش در کارت‌های جدا هستند." icon="ki-filled ki-category" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="عنوان و نشانی صفحهٔ این بخش" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام بخش" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات بخش">
                <x-admin.form-section title="ظاهر صفحهٔ بخش" description="رنگ و تصویر هدر این بخش" icon="ki-filled ki-color-swatch">
                    <x-form.input name="bg_color" type="color" label="رنگ پس‌زمینه" value="#ffffff" />
                    <x-form.file name="bg_image" label="تصویر پس‌زمینه" accept="image/*" hint="اختیاری؛ تصویر حداکثر ۵ مگابایت است." class="sm:col-span-2" />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت بخش در فروشگاه" icon="ki-filled ki-eye">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value" required />
                </x-admin.form-section>
            </aside>
            <div class="admin-form-main col-span-full">
                <x-admin.form-section title="آیتم‌های بخش" description="آیتم‌هایی را انتخاب کنید که در این بخش نشان داده می‌شوند." icon="ki-filled ki-package">
                    <livewire:admin.sections.section-item-picker :selected-item-ids="old('item_ids', []) ?? []" />
                </x-admin.form-section>
                <x-admin.form-section title="گالری بخش" description="تصاویر گالری را در جایگاه‌های جدا اضافه کنید." icon="ki-filled ki-picture">
                    <x-form.image-gallery name="images" class="sm:col-span-2" />
                    @error('images') <p class="text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                    @error('images.*') <p class="text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
