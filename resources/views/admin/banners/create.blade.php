<x-panel-layout title="افزودن بنر" sub-title="تصویر و مقصد نمایش بنر را تنظیم کنید.">
    <x-card.form
        action="{{ route('admin.banners.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_banner_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.banners.index')]"
        :submit="['title' => 'ایجاد بنر', 'type' => 'primary']"
    >
        <x-admin.form-intro title="بنر فروشگاه" description="تصویر بنر را انتخاب کنید و مقصد کلیک را در تنظیمات کناری مشخص کنید." icon="ki-filled ki-picture" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="تصویر بنر" description="تصویر اصلی که به کاربران نمایش داده می‌شود." icon="ki-filled ki-picture">
                    <x-form.file name="image" label="تصویر بنر" accept="image/*" required hint="تصویر حداکثر ۵ مگابایت است و به‌صورت محلی ذخیره می‌شود." class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="مقصد بنر" description="نوع مقصد را انتخاب و فیلد مربوط به آن را کامل کنید." icon="ki-filled ki-exit-right-corner">
                    <x-form.select name="type" label="نوع مقصد" :options="$typeOptions" :selected="\App\Enums\BannerTypeEnum::LINK->value" required />
                    <x-form.select name="product_id" label="محصول مقصد" :options="$products" placeholder="انتخاب محصول" hint="برای نوع محصول انتخاب شود." />
                    <x-form.select name="category_id" label="دسته‌بندی مقصد" :options="$categories" placeholder="انتخاب دسته‌بندی" hint="برای نوع دسته‌بندی انتخاب شود." />
                    <x-form.input name="link" label="پیوند مقصد" placeholder="https://example.com" dir="ltr" hint="برای نوع پیوند وارد شود." class="sm:col-span-2" />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت بنر" icon="ki-filled ki-eye">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value" required />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
