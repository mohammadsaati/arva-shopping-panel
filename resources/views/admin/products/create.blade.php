<x-panel-layout
    title="افزودن محصول یا تنوع"
    sub-title="هر محصول به یک آیتم متصل است و می‌تواند رنگ و سایز جداگانه داشته باشد."
>
    <div class="mb-5 flex items-center justify-end">
        <x-button href="{{ route('admin.products.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست محصولات
        </x-button>
    </div>

    @if (! $hasItems)
        <x-alert type="warning" class="mb-5">
            ابتدا یک آیتم ایجاد کنید؛ هر محصول باید به یک آیتم متصل باشد.
            <a href="{{ route('admin.items.create') }}" class="font-semibold underline">افزودن آیتم</a>
        </x-alert>
    @endif

    <x-card.form
        action="{{ route('admin.products.store') }}"
        method="POST"
        enctype="multipart/form-data"
        id="create_product_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.products.index')]"
        :submit="['title' => 'افزودن محصول', 'type' => 'primary']"
    >
        <x-admin.form-intro title="محصول و تنوع" description="ابتدا آیتم و ویژگی‌های تنوع را انتخاب کنید؛ سپس قیمت، موجودی و روش ارسال را کامل کنید." icon="ki-filled ki-package" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="آیتم و تنوع" description="این محصول به کدام آیتم و ویژگی‌ها تعلق دارد؟" icon="ki-filled ki-category" :two-columns="true">
            <x-form.select2
                name="item_id"
                label="آیتم مرتبط"
                :selected="$selectedItemId"
                :route="route('admin.items.select-options')"
                placeholder="انتخاب آیتم"
                hint="نام یا کد آیتم را جستجو کنید."
                :clearable="false"
                required
            />
            <x-form.select name="color_id" label="رنگ" :options="$colors" placeholder="بدون رنگ" />
            <x-form.select name="size_id" label="سایز" :options="$sizes" placeholder="بدون سایز" />
                </x-admin.form-section>
                <x-admin.form-section title="قیمت و موجودی" description="قیمت فروش، تعداد موجود و هزینه ارسال پیش‌فرض" icon="ki-filled ki-price-tag" :two-columns="true">
            <x-form.input name="price" label="قیمت (تومان)" type="number" min="0" dir="ltr" required />
            <x-form.input name="stock" label="موجودی" type="number" min="0" dir="ltr" value="0" required />
            <x-form.input name="shipping_price" label="هزینه ارسال پیش‌فرض (تومان)" type="number" min="0" dir="ltr" value="0" required />
                </x-admin.form-section>
                <x-admin.form-section title="هزینه ارسال شهرها" description="برای شهرهایی که نرخ متفاوت دارند مقدار جداگانه ثبت کنید." icon="ki-filled ki-delivery-3">
                    <x-admin.product-city-rates :city-options="$cities" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات محصول">
                <x-admin.form-section title="نمایش محصول" description="وضعیت نمایش و فروش" icon="ki-filled ki-eye">
            <x-form.select
                name="status"
                label="وضعیت"
                :options="$statusOptions"
                :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value"
                required
            />
                </x-admin.form-section>
                <x-admin.form-section title="گالری تصاویر" description="تصاویر مخصوص همین تنوع محصول را انتخاب کنید." icon="ki-filled ki-picture">
                    <x-form.file name="images" label="تصاویر گالری محصول" accept="image/*" multiple hint="حداکثر ۱۰ تصویر، هر تصویر تا ۵ مگابایت." />
                    @error('images.*') <p class="mt-1 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
