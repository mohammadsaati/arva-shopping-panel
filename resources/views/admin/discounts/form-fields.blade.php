@php
    $selectedProductIds = old('product_ids', isset($discount) && ! old('product_ids_present') ? $discount->products->modelKeys() : []);
    $selectedCategoryIds = old('category_ids', isset($discount) && ! old('category_ids_present') ? $discount->categories->modelKeys() : []);
    $selectedUserId = old('user_id', $discount->user_id ?? null);
    $selectedType = isset($discount) ? $discount->type->value : \App\Enums\DiscountTypeEnum::PERCENTAGE->value;
    $selectedStatus = isset($discount) ? $discount->status->value : \App\Enums\GlobalStatusEnum::ACTIVE->value;
@endphp

<x-admin.form-intro title="کد تخفیف" description="کد و مقدار را تنظیم کنید؛ شرایط استفاده، زمان و مخاطبان در کارت‌های جدا هستند." icon="ki-filled ki-discount" />
<div class="admin-form-layout">
    <div class="admin-form-main">
        <x-admin.form-section title="هویت و مقدار تخفیف" description="این کد با چه نام و مقداری ثبت می‌شود؟" icon="ki-filled ki-discount" :two-columns="true">
            <x-form.input name="name" label="نام داخلی تخفیف" :value="old('name', $discount->name ?? '')" required />
            <x-form.input name="code" label="کد تخفیف" :value="old('code', $discount->code ?? '')" dir="ltr" hint="کد هنگام ذخیره به حروف بزرگ تبدیل می‌شود." required />
            <x-form.select name="type" label="نوع تخفیف" :options="$typeOptions" :selected="old('type', $selectedType)" required />
            <x-form.input name="amount" label="مقدار تخفیف" type="number" min="1" :value="old('amount', $discount->amount ?? '')" hint="برای تخفیف درصدی عددی بین ۱ تا ۱۰۰ و برای مبلغ ثابت مقدار تومان وارد کنید." required dir="ltr" />
        </x-admin.form-section>
        <x-admin.form-section title="دامنهٔ استفاده" description="کالاها، دسته‌بندی‌ها و کاربر مجاز را انتخاب کنید." icon="ki-filled ki-category">
            <livewire:admin.discounts.discount-target-picker
                :selected-product-ids="is_array($selectedProductIds) ? $selectedProductIds : []"
                :selected-category-ids="is_array($selectedCategoryIds) ? $selectedCategoryIds : []"
                :selected-user-id="is_numeric($selectedUserId) && (int) $selectedUserId > 0 ? (int) $selectedUserId : null"
            />
        </x-admin.form-section>
    </div>
    <aside class="admin-form-sidebar" aria-label="شرایط کد تخفیف">
        <x-admin.form-section title="شرایط مبلغ" description="حداقل سفارش و سقف تخفیف اختیاری هستند." icon="ki-filled ki-price-tag">
            <x-form.input name="min_order_amount" label="حداقل مبلغ کالاها (تومان)" type="number" min="0" :value="old('min_order_amount', $discount->min_order_amount ?? '')" dir="ltr" />
            <x-form.input name="max_discount_amount" label="سقف مبلغ تخفیف (تومان)" type="number" min="1" :value="old('max_discount_amount', $discount->max_discount_amount ?? '')" dir="ltr" hint="برای محدود کردن تخفیف درصدی." />
        </x-admin.form-section>
        <x-admin.form-section title="زمان اعتبار" description="در صورت خالی بودن، محدودیت زمانی اعمال نمی‌شود." icon="ki-filled ki-calendar">
            <x-date-picket name="starts_at" label="شروع اعتبار" :value="old('starts_at', isset($discount) && $discount->starts_at ? jalali_date($discount->starts_at, 'Y/m/d H:i:s') : '')" placeholder="بدون محدودیت یا انتخاب زمان" />
            <x-date-picket name="ends_at" label="پایان اعتبار" :value="old('ends_at', isset($discount) && $discount->ends_at ? jalali_date($discount->ends_at, 'Y/m/d H:i:s') : '')" placeholder="بدون محدودیت یا انتخاب زمان" />
        </x-admin.form-section>
        <x-admin.form-section title="محدودیت استفاده" description="تعداد دفعات استفاده را تعیین کنید." icon="ki-filled ki-setting-2">
            <x-form.input name="usage_limit" label="سقف کل استفاده" type="number" min="1" :value="old('usage_limit', $discount->usage_limit ?? '')" hint="خالی بماند یعنی بدون محدودیت." dir="ltr" />
            <x-form.input name="usage_limit_per_user" label="سقف استفاده برای هر کاربر" type="number" min="1" :value="old('usage_limit_per_user', $discount->usage_limit_per_user ?? '')" hint="خالی بماند یعنی بدون محدودیت." dir="ltr" />
            <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="old('status', $selectedStatus)" required />
        </x-admin.form-section>
    </aside>
</div>
