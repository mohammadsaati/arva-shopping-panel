<x-panel-layout
    title="افزودن کاربر"
    sub-title="مشخصات حساب کاربری جدید را وارد کنید."
>
    <div class="mb-5 flex items-center justify-end gap-3">
        <x-button href="{{ route('admin.users.index') }}" variant="light" icon="ki-filled ki-arrow-right">
            بازگشت به فهرست
        </x-button>
    </div>

    <x-card.form
        action="{{ route('admin.users.store') }}"
        method="POST"
        id="create_user_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.users.index')]"
        :submit="['title' => 'ایجاد کاربر', 'type' => 'primary']"
    >
        <x-admin.form-intro title="حساب کاربر" description="مشخصات هویتی را ثبت کنید؛ نوع حساب و رمز عبور در ستون کناری قرار دارند." icon="ki-filled ki-profile-circle" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات کاربر" description="نام و شماره تلفنی که برای حساب استفاده می‌شود." icon="ki-filled ki-profile-circle" :two-columns="true">
            <x-form.input
                name="first_name"
                label="نام"
                autocomplete="given-name"
                required
            />
            <x-form.input
                name="last_name"
                label="نام خانوادگی"
                autocomplete="family-name"
                required
            />
            <x-form.input
                name="phone"
                label="شماره تلفن"
                type="tel"
                dir="ltr"
                autocomplete="tel"
                required
            />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات حساب">
                <x-admin.form-section title="دسترسی" description="نوع حساب کاربری" icon="ki-filled ki-setting-2">
            <x-form.select
                name="type"
                label="نوع حساب"
                :options="$userTypes"
                :selected="\App\Enums\UserTypeEnum::NORMAL->value"
                required
            />
                </x-admin.form-section>
                <x-admin.form-section title="رمز عبور" description="رمزی امن برای ورود کاربر تعیین کنید." icon="ki-filled ki-lock-2">
            <x-form.input
                name="password"
                label="رمز عبور"
                type="password"
                autocomplete="new-password"
                hint="رمز عبور باید حداقل ۸ کاراکتر باشد."
                required
            />
            <x-form.input
                name="password_confirmation"
                label="تکرار رمز عبور"
                type="password"
                autocomplete="new-password"
                required
            />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
