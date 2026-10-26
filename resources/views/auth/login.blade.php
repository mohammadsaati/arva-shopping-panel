<x-user-layout :title="'ورود به حساب کاربری'" :chrome="false">
    <x-auth.stage
        mode="login"
        title="دوباره سلام"
        subtitle="شماره و رمز را بزن تا سبد و سفارشت همین‌جا منتظر بماند."
        welcome-title="خرید قشنگ از همین‌جا شروع می‌شود"
        welcome-text="وارد شو تا سبدت گم نشود، نشانی‌ات ذخیره بماند و پرداخت یک‌نفس تمام شود."
    >
        @if (session('success'))
            <div class="user-auth-alert user-auth-alert--success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->has('auth'))
            <div class="user-auth-alert" role="alert">{{ $errors->first('auth') }}</div>
        @endif

        <x-card.form
            class="user-auth-form"
            :action="route('login.store')"
            :submit="['title' => 'ورود به حساب', 'type' => 'primary', 'icon' => 'ki-filled ki-arrow-left']"
        >
            <x-form.input
                name="phone"
                type="tel"
                label="شماره موبایل"
                placeholder="0912 000 0000"
                hint="با ۰۹ شروع کن؛ همان شماره‌ای که با آن ثبت‌نام کردی."
                autocomplete="tel"
                inputmode="tel"
                dir="ltr"
                icon="ki-filled ki-phone"
                required
                autofocus
            />
            <x-form.input
                name="password"
                type="password"
                label="رمز عبور"
                placeholder="رمز عبورت را بنویس"
                autocomplete="current-password"
                icon="ki-filled ki-lock"
                revealable
                required
            />
        </x-card.form>

        <p class="user-auth-switch">
            <a href="{{ route('password.request') }}">رمز عبور را فراموش کرده‌ام</a>
        </p>
        <p class="user-auth-switch">
            هنوز حساب نداری؟
            <a href="{{ route('register') }}">یکی بساز</a>
        </p>
        <p class="user-auth-legal">ورود یعنی ادامه خرید با همین حساب.</p>
        <a class="user-auth-home-link" href="{{ route('home') }}">بازگشت به فروشگاه</a>
    </x-auth.stage>
</x-user-layout>
