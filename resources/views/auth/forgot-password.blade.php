<x-user-layout :title="'بازیابی رمز عبور'" :chrome="false">
    <x-auth.stage
        mode="login"
        title="رمز را فراموش کردی؟"
        subtitle="شماره موبایلت را بزن؛ اگر ایمیل بازیابی در حسابت ثبت شده باشد، لینک می‌فرستیم."
        welcome-title="بازیابی امن رمز"
        welcome-text="ایمیل بازیابی را از بخش حساب کاربری می‌توانی ثبت یا به‌روز کنی."
    >
        @if (session('success'))
            <div class="user-auth-alert user-auth-alert--success" role="status">{{ session('success') }}</div>
        @endif
        @error('phone')
            <div class="user-auth-alert" role="alert">{{ $message }}</div>
        @enderror

        <x-card.form
            class="user-auth-form"
            :action="route('password.email')"
            :submit="['title' => 'ارسال لینک بازیابی', 'type' => 'primary', 'icon' => 'ki-filled ki-sms']"
        >
            <x-form.input
                name="phone"
                type="tel"
                label="شماره موبایل"
                placeholder="0912 000 0000"
                autocomplete="tel"
                inputmode="tel"
                dir="ltr"
                icon="ki-filled ki-phone"
                required
                autofocus
            />
        </x-card.form>

        <p class="user-auth-meta">
            <a href="{{ route('login') }}">بازگشت به ورود</a>
        </p>
    </x-auth.stage>
</x-user-layout>
