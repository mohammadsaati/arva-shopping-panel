<x-user-layout :title="'ساخت حساب کاربری'" :chrome="false">
    <x-auth.stage
        mode="register"
        title="بیا داخل جمع قشنگ‌ها"
        subtitle="چند خط کافی است تا سبد، نشانی و پیگیری سفارش مال خودت شود."
        welcome-title="حسابت را مثل یک کیف قشنگ بردار"
        welcome-text="ثبت‌نام رایگان است. بعدش کالا را نگه می‌داری، نشانی می‌سازی و بدون معطلی سفارش می‌دهی."
    >
        <x-card.form
            class="user-auth-form"
            :action="route('register.store')"
            :submit="['title' => 'ساخت حساب', 'type' => 'primary', 'icon' => 'ki-filled ki-plus']"
        >
            <div class="user-auth-name-fields">
                <x-form.input
                    name="first_name"
                    label="نام"
                    placeholder="مثلاً سارا"
                    autocomplete="given-name"
                    icon="ki-filled ki-profile-circle"
                    required
                    autofocus
                />
                <x-form.input
                    name="last_name"
                    label="نام خانوادگی"
                    placeholder="مثلاً محمدی"
                    autocomplete="family-name"
                    icon="ki-filled ki-profile-circle"
                    required
                />
            </div>
            <x-form.input
                name="phone"
                type="tel"
                label="شماره موبایل"
                placeholder="0912 000 0000"
                hint="برای ورود بعدی همین شماره را می‌خواهیم."
                autocomplete="tel"
                inputmode="tel"
                dir="ltr"
                icon="ki-filled ki-phone"
                required
            />
            <x-form.input
                name="password"
                type="password"
                label="رمز عبور"
                placeholder="حداقل ۸ نویسه"
                hint="حداقل ۸ نویسه. عدد و حرف با هم بهتر است."
                autocomplete="new-password"
                icon="ki-filled ki-lock"
                revealable
                minlength="8"
                required
                data-auth-password
            />
            <div class="user-auth-pass" data-auth-pass-meter hidden>
                <span></span>
                <span></span>
                <span></span>
                <small data-auth-pass-label></small>
            </div>
            <x-form.input
                name="password_confirmation"
                type="password"
                label="تکرار رمز عبور"
                placeholder="همان رمز را دوباره بنویس"
                autocomplete="new-password"
                icon="ki-filled ki-lock"
                revealable
                minlength="8"
                required
                data-auth-password-confirm
            />
            <p class="user-auth-match" data-auth-pass-match hidden></p>
        </x-card.form>

        <p class="user-auth-switch">
            قبلاً ثبت‌نام کرده‌ای؟
            <a href="{{ route('login') }}">وارد شو</a>
        </p>
        <p class="user-auth-legal">ساخت حساب فقط برای خرید و پیگیری سفارش است.</p>
        <a class="user-auth-home-link" href="{{ route('home') }}">بازگشت به فروشگاه</a>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const password = document.querySelector('[data-auth-password]');
                const confirm = document.querySelector('[data-auth-password-confirm]');
                const meter = document.querySelector('[data-auth-pass-meter]');
                const label = document.querySelector('[data-auth-pass-label]');
                const match = document.querySelector('[data-auth-pass-match]');
                if (!password || !confirm || !meter || !label || !match) return;

                const score = function (value) {
                    let points = 0;
                    if (value.length >= 8) points += 1;
                    if (value.length >= 8 && /[0-9]/.test(value) && /[A-Za-zآ-ی]/.test(value)) points += 1;
                    if (value.length >= 12) points += 1;
                    return points;
                };

                const paint = function () {
                    const value = password.value || '';
                    const points = score(value);
                    const texts = ['کوتاه است', 'قابل قبول', 'خوب و محکم'];
                    meter.hidden = value.length === 0;
                    meter.dataset.score = String(points);
                    label.textContent = texts[Math.max(0, points - 1)] || texts[0];
                    if (value.length > 0 && value.length < 8) {
                        label.textContent = 'هنوز به ۸ نویسه نرسیده';
                    }

                    const same = confirm.value.length > 0 && confirm.value === value;
                    match.hidden = confirm.value.length === 0;
                    match.textContent = same ? 'هر دو رمز یکی است.' : 'این دو تا هنوز یکی نیستند.';
                    match.classList.toggle('is-ok', same);
                };

                password.addEventListener('input', paint);
                confirm.addEventListener('input', paint);
            });
        </script>
    </x-auth.stage>
</x-user-layout>
