<x-panel-layout title="افزودن محصول" sub-title="محصولی را به یکی از آیتم‌های خود اضافه کنید.">
    @if ($items === [])
        <x-alert type="warning">ابتدا یک آیتم بسازید. <a href="{{ route('vendor.items.create') }}" class="underline">افزودن آیتم</a></x-alert>
    @endif
    <x-card.form action="{{ route('vendor.products.store') }}" method="POST" enctype="multipart/form-data"
        class="admin-editor-form" :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('vendor.products.index')]"
        :submit="['title' => 'ثبت محصول', 'type' => 'primary']">
        <x-admin.form-intro title="محصول جدید" description="فقط آیتم‌های خودتان قابل انتخاب هستند. محصول پس از ثبت برای تأیید ادمین می‌رود." icon="ki-filled ki-package" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="آیتم و تنوع" description="محصول به کدام آیتم تعلق دارد؟" icon="ki-filled ki-category" :two-columns="true">
                    <x-form.select name="item_id" label="آیتم" :options="$items" :selected="$selectedItemId" placeholder="انتخاب آیتم" required />
                    <x-form.select name="color_id" label="رنگ" :options="$colors" placeholder="بدون رنگ" />
                    <x-form.select name="size_id" label="سایز" :options="$sizes" placeholder="بدون سایز" />
                </x-admin.form-section>
                <x-admin.form-section title="قیمت و موجودی" description="قیمت فروش و تعداد موجود را تعیین کنید." icon="ki-filled ki-price-tag" :two-columns="true">
                    <x-form.input name="price" label="قیمت (تومان)" type="number" min="0" dir="ltr" required />
                    <x-form.input name="stock" label="موجودی" type="number" min="0" dir="ltr" required />
                    <x-form.input name="shipping_price" label="هزینه ارسال (تومان)" type="number" min="0" dir="ltr" value="0" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تصاویر محصول">
                <x-admin.form-section title="تصاویر" description="تصاویر این محصول" icon="ki-filled ki-picture">
                    <x-form.file name="images" label="گالری محصول" accept="image/*" multiple hint="اختیاری، حداکثر ۱۰ تصویر" />
                    @error('images.*') <p class="text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
