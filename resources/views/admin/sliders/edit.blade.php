<x-panel-layout title="ویرایش اسلایدر" sub-title="اطلاعات و مقصد اسلاید را ویرایش کنید.">
    <x-card.form
        action="{{ route('admin.sliders.update', $slider->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        id="edit_slider_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.sliders.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="اسلاید صفحهٔ اصلی" description="عنوان و تصویر اسلاید را آماده کنید؛ مقصد و ترتیب نمایش در ستون کناری هستند." icon="ki-filled ki-picture" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="محتوای اسلاید" description="عنوان و تصویری که در اسلایدر دیده می‌شود." icon="ki-filled ki-picture">
                    <x-form.input name="title" label="عنوان" :value="$slider->title" required />
                    <x-form.file name="image" label="تصویر اسلایدر" accept="image/*" :current-image="Storage::disk('public')->url($slider->image)" hint="برای نگه‌داشتن تصویر فعلی، فایل جدید انتخاب نکنید." class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="مقصد اسلاید" description="برای نوع مقصد انتخاب‌شده، فیلد مربوط را کامل کنید." icon="ki-filled ki-exit-right-corner">
                    <x-form.select name="type" label="نوع مقصد" :options="$typeOptions" :selected="$slider->type->value" required />
                    <x-form.select name="product_id" label="محصول مقصد" :options="$products" placeholder="بدون محصول" :selected="$slider->product_id" />
                    <x-form.select name="category_id" label="دسته‌بندی مقصد" :options="$categories" placeholder="بدون دسته‌بندی" :selected="$slider->category_id" />
                    <x-form.select name="section_id" label="بخش مقصد" :options="$sections" placeholder="بدون بخش" :selected="$slider->section_id" />
                    <x-form.input name="link" label="پیوند مقصد" :value="$slider->link" placeholder="https://example.com" dir="ltr" />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش و ترتیب" description="وضعیت و اولویت اسلاید" icon="ki-filled ki-setting-2">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$slider->status->value" required />
                    <x-form.input name="priority" label="اولویت نمایش" type="number" min="0" :value="$slider->priority" dir="ltr" required />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
