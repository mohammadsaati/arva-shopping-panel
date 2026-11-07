<x-panel-layout title="افزودن آیتم" sub-title="آیتم جدیدی برای فروشگاه خود ثبت کنید.">
    <x-card.form action="{{ route('vendor.items.store') }}" method="POST" enctype="multipart/form-data"
        class="admin-editor-form" :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('vendor.items.index')]"
        :submit="['title' => 'ثبت آیتم', 'type' => 'primary']">
        <x-admin.form-intro title="آیتم جدید" description="نام و دسته‌بندی کالا را وارد کنید؛ سپس می‌توانید محصولات آن را بسازید." icon="ki-filled ki-category" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="هویت آیتم" description="نام، شناسه و کد یکتا" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام آیتم" required />
                    <x-form.input name="slug" label="شناسه متنی" dir="ltr" required />
                    <x-form.input name="code" label="کد آیتم" type="number" min="1" dir="ltr" required />
                </x-admin.form-section>
                <x-admin.form-section title="دسته و توضیح" description="آیتم در کدام بخش فروشگاه نمایش داده شود؟" icon="ki-filled ki-category" :two-columns="true">
                    <x-form.select name="category_id" label="دسته‌بندی" :options="$categories" placeholder="انتخاب دسته‌بندی" required />
                    <x-form.select name="brand_id" label="برند" :options="$brands" placeholder="بدون برند" />
                    <x-form.textarea name="description" label="توضیحات" rows="4" class="sm:col-span-2" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تصویر آیتم">
                <x-admin.form-section title="تصویر" description="تصویر اصلی آیتم" icon="ki-filled ki-picture">
                    <x-form.file name="image" label="تصویر آیتم" accept="image/*" hint="اختیاری، حداکثر ۵ مگابایت" />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
