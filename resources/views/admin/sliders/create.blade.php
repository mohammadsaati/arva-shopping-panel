<x-panel-layout title="افزودن اسلایدر" sub-title="تصویر، اولویت نمایش و مقصد اسلاید را تنظیم کنید.">
    <x-card.form
        action="{{ route('admin.sliders.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_slider_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.sliders.index')]"
        :submit="['title' => 'ایجاد اسلایدر', 'type' => 'primary']"
    >
        <x-admin.form-intro title="اسلاید صفحهٔ اصلی" description="عنوان و تصویر اسلاید را آماده کنید؛ مقصد و ترتیب نمایش در ستون کناری هستند." icon="ki-filled ki-picture" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="محتوای اسلاید" description="عنوان و تصویری که در اسلایدر دیده می‌شود." icon="ki-filled ki-picture">
                    <x-form.input name="title" label="عنوان" required />
                    <x-form.file name="image" label="تصویر اسلایدر" accept="image/*" required hint="تصویر حداکثر ۵ مگابایت است و به‌صورت محلی ذخیره می‌شود." class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="مقصد اسلاید" description="برای نوع مقصد انتخاب‌شده، فیلد مربوط را کامل کنید." icon="ki-filled ki-exit-right-corner">
                    <x-form.select name="type" label="نوع مقصد" :options="$typeOptions" :selected="\App\Enums\SliderTypeEnum::LINK->value" required />
                    <x-form.select name="product_id" label="محصول مقصد" :options="$products" placeholder="انتخاب محصول" hint="برای نوع محصول انتخاب شود." />
                    <x-form.select name="category_id" label="دسته‌بندی مقصد" :options="$categories" placeholder="انتخاب دسته‌بندی" hint="برای نوع دسته‌بندی انتخاب شود." />
                    <x-form.select name="section_id" label="بخش مقصد" :options="$sections" placeholder="انتخاب بخش" hint="برای نوع بخش فروشگاه انتخاب شود." />
                    <x-form.input name="link" label="پیوند مقصد" placeholder="https://example.com" dir="ltr" hint="برای نوع پیوند وارد شود." />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش و ترتیب" description="وضعیت و اولویت اسلاید" icon="ki-filled ki-setting-2">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value" required />
                    <x-form.input name="priority" label="اولویت نمایش" type="number" min="0" value="0" dir="ltr" required />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
