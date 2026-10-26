<x-user-layout :title="'تنظیم رمز جدید'" :chrome="false">
    <x-auth.stage
        mode="login"
        title="رمز جدید"
        subtitle="رمز تازه‌ات را وارد کن و دوباره وارد فروشگاه شو."
        welcome-title="یک قدم تا ورود"
        welcome-text="لینک بازیابی محدود به زمان است؛ اگر منقضی شد دوباره درخواست بده."
    >
        @error('token')
            <div class="user-auth-alert" role="alert">{{ $message }}</div>
        @enderror
        @error('phone')
            <div class="user-auth-alert" role="alert">{{ $message }}</div>
        @enderror

        <x-card.form
            class="user-auth-form"
            :action="route('password.update')"
            :submit="['title' => 'ذخیره رمز جدید', 'type' => 'primary', 'icon' => 'ki-filled ki-lock']"
        >
            <input type="hidden" name="token" value="{{ $token }}">
            <x-form.input
                name="phone"
                type="tel"
                label="شماره موبایل"
                :value="old('phone', $phone)"
                autocomplete="tel"
                inputmode="tel"
                dir="ltr"
                icon="ki-filled ki-phone"
                required
            />
            <x-form.input
                name="password"
                type="password"
                label="رمز جدید"
                autocomplete="new-password"
                icon="ki-filled ki-lock"
                revealable
                required
            />
            <x-form.input
                name="password_confirmation"
                type="password"
                label="تکرار رمز جدید"
                autocomplete="new-password"
                icon="ki-filled ki-lock"
                revealable
                required
            />
        </x-card.form>
    </x-auth.stage>
</x-user-layout>
