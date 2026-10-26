<x-panel-layout title="ویرایش بنر" sub-title="اطلاعات مقصد و وضعیت بنر را ویرایش کنید.">
    <x-card.form
        action="{{ route('admin.banners.update', $banner->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        id="edit_banner_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.banners.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="بنر فروشگاه" description="تصویر بنر را انتخاب کنید و مقصد کلیک را در تنظیمات کناری مشخص کنید." icon="ki-filled ki-picture" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="تصویر بنر" description="تصویر اصلی که به کاربران نمایش داده می‌شود." icon="ki-filled ki-picture">
                    <x-form.file name="image" label="تصویر بنر" accept="image/*" :current-image="Storage::disk('public')->url($banner->image)" hint="برای نگه‌داشتن تصویر فعلی، فایل جدید انتخاب نکنید." class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="مقصد بنر" description="نوع مقصد را انتخاب و فیلد مربوط به آن را کامل کنید." icon="ki-filled ki-exit-right-corner">
                    <x-form.select name="type" label="نوع مقصد" :options="$typeOptions" :selected="$banner->type->value" required />
                    <x-form.select name="product_id" label="محصول مقصد" :options="$products" placeholder="بدون محصول" :selected="$banner->product_id" />
                    <x-form.select name="category_id" label="دسته‌بندی مقصد" :options="$categories" placeholder="بدون دسته‌بندی" :selected="$banner->category_id" />
                    <x-form.input name="link" label="پیوند مقصد" :value="$banner->link" placeholder="https://example.com" dir="ltr" class="sm:col-span-2" />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت بنر" icon="ki-filled ki-eye">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$banner->status->value" required />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
