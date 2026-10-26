<x-user.account-shell title="اطلاعات حساب" sub-title="مشخصات فردی و امنیت حساب خود را مدیریت کنید.">
    @if (session('success'))
        <div class="user-account-alert user-account-alert--success" role="status">{{ session('success') }}</div>
    @endif

    <div class="user-account-intro">
        <span class="user-account-intro__icon" aria-hidden="true"><i class="ki-filled ki-user"></i></span>
        <div>
            <h2>مشخصات فردی</h2>
            <p>اطلاعات تماس و مشخصات خود را در این بخش به‌روز کنید.</p>
        </div>
    </div>

    <div class="user-account-forms">
        <x-card.form
            :action="route('account.update')"
            method="PATCH"
            title="اطلاعات شخصی"
            :submit="['title' => 'ذخیره اطلاعات', 'type' => 'primary']"
        >
            <div class="user-account-name-fields">
                <x-form.input
                    name="first_name"
                    label="نام"
                    :value="$user->first_name"
                    autocomplete="given-name"
                    icon="ki-filled ki-profile-circle"
                    required
                />
                <x-form.input
                    name="last_name"
                    label="نام خانوادگی"
                    :value="$user->last_name"
                    autocomplete="family-name"
                    icon="ki-filled ki-profile-circle"
                    required
                />
            </div>
            <x-form.input
                name="phone"
                type="tel"
                label="شماره موبایل"
                :value="$user->phone"
                autocomplete="tel"
                dir="ltr"
                icon="ki-filled ki-phone"
                inputmode="tel"
                required
            />
            <x-form.input
                name="recovery_email"
                type="email"
                label="ایمیل بازیابی رمز"
                :value="$user->recovery_email"
                hint="برای دریافت لینک بازیابی رمز در صورت فراموشی."
                autocomplete="email"
                dir="ltr"
                icon="ki-filled ki-sms"
            />
            <x-form.input
                name="job"
                label="شغل"
                :value="$user->profile?->job"
                autocomplete="organization-title"
                icon="ki-filled ki-briefcase"
            />
            <x-form.input
                name="bank_card"
                label="شماره کارت بانکی"
                :value="$user->profile?->bank_card"
                inputmode="numeric"
                dir="ltr"
                icon="ki-filled ki-credit-cart"
                autocomplete="off"
            />
        </x-card.form>

        <x-card.form
            :action="route('account.password.update')"
            method="PUT"
            title="تغییر رمز عبور"
            :submit="['title' => 'تغییر رمز عبور', 'type' => 'primary']"
        >
            <x-form.input
                name="current_password"
                type="password"
                label="رمز عبور فعلی"
                autocomplete="current-password"
                icon="ki-filled ki-lock"
                revealable
                required
            />
            <x-form.input
                name="password"
                type="password"
                label="رمز عبور جدید"
                placeholder="حداقل ۸ نویسه"
                autocomplete="new-password"
                icon="ki-filled ki-lock"
                revealable
                required
            />
            <x-form.input
                name="password_confirmation"
                type="password"
                label="تکرار رمز عبور جدید"
                autocomplete="new-password"
                icon="ki-filled ki-lock"
                revealable
                required
            />
        </x-card.form>
    </div>
</x-user.account-shell>
