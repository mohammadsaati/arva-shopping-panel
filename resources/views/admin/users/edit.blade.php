<x-panel-layout
    title="ویرایش کاربر"
    sub-title="اطلاعات حساب و وضعیت کاربر را ویرایش کنید."
>
    <x-card.form
        action="{{ route('admin.users.update', $user->id) }}"
        method="PUT"
        id="edit_user_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.users.show', $user->id)]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="ویرایش حساب کاربر" description="اطلاعات اصلی، وضعیت حساب و پروفایل در بخش‌های مستقل قرار دارند." icon="ki-filled ki-profile-circle" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات کاربر" description="نام و شماره تلفن کاربر" icon="ki-filled ki-profile-circle" :two-columns="true">
            <x-form.input name="first_name" label="نام" :value="$user->first_name" autocomplete="given-name" required />
            <x-form.input name="last_name" label="نام خانوادگی" :value="$user->last_name" autocomplete="family-name" required />
            <x-form.input name="phone" label="شماره تلفن" type="tel" :value="$user->phone" dir="ltr" autocomplete="tel" required />
                </x-admin.form-section>
                <x-admin.form-section title="اطلاعات پروفایل" description="مقادیر خالی از پروفایل پاک می‌شوند." icon="ki-filled ki-user" :two-columns="true">
                    <x-form.input name="bank_card" label="شماره کارت بانکی" :value="$user->profile?->bank_card" dir="ltr" />
                    <x-form.input name="job" label="شغل" :value="$user->profile?->job" />
                    <x-form.input name="city_id" label="شناسه شهر" :value="$user->profile?->city_id" dir="ltr" />
                </x-admin.form-section>
                <div id="vendor_fields" @if (old('type', $user->type->value) !== \App\Enums\UserTypeEnum::VENDOR->value) hidden @endif>
                    <x-admin.form-section title="اطلاعات فروشنده" description="مشخصات فروشگاه و کمیسیون اختیاری این فروشنده." icon="ki-filled ki-shop" :two-columns="true">
                        <x-form.input name="vendor_company_name" label="نام شرکت یا فروشگاه" :value="$user->vendor?->company_name" required />
                        <x-form.select name="vendor_city_id" label="شهر فروشنده" :options="$vendorCityOptions" :selected="$user->vendor?->city_id" placeholder="شهر را انتخاب کنید" required />
                        <x-form.select name="commission_id" label="کمیسیون" :options="$vendorCommissionOptions" :selected="$user->vendor?->commission_id" placeholder="بدون کمیسیون" />
                    </x-admin.form-section>
                </div>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات حساب">
                <x-admin.form-section title="دسترسی و نمایش" description="نوع و وضعیت حساب کاربر" icon="ki-filled ki-setting-2">
            <x-form.select name="type" label="نوع حساب" :options="$userTypes" :selected="$user->type->value" required />
            <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$user->status->value" required />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const type = document.getElementById('select_type');
                const section = document.getElementById('vendor_fields');
                const fields = section?.querySelectorAll('input, select') ?? [];
                const toggle = () => {
                    const isVendor = type?.value === @json(\App\Enums\UserTypeEnum::VENDOR->value);
                    section.hidden = !isVendor;
                    fields.forEach((field) => {
                        field.disabled = !isVendor;
                        field.required = isVendor && field.name !== 'commission_id';
                    });
                };
                type?.addEventListener('change', toggle);
                toggle();
            });
        </script>
    @endpush
</x-panel-layout>
