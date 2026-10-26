@extends('layouts.auth')

@section('content')
    <main class="admin-login" aria-labelledby="admin-login-title">
        <section class="admin-login__form-panel">
            <a href="{{ route('home') }}" class="admin-login__mobile-brand" aria-label="{{ site_name() }}، صفحه اصلی">
                <span class="admin-login__mark" aria-hidden="true">{{ mb_substr(site_name(), 0, 1) }}</span>
                <span>{{ site_name() }}</span>
            </a>

            <div class="admin-login__intro">
                <span class="admin-login__eyebrow"><span aria-hidden="true"></span> فضای مدیریت فروشگاه</span>
                <h1 id="admin-login-title">خوش آمدید</h1>
                <p>برای ادامه، با حساب مدیر وارد پنل شوید.</p>
            </div>

            @if ($errors->has('auth'))
                <div class="admin-login__error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 8v4m0 4h.01M10.3 3.86 1.82 18.14A2 2 0 0 0 3.53 21h16.94a2 2 0 0 0 1.71-2.86L13.7 3.86a2 2 0 0 0-3.4 0Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>{{ $errors->first('auth') }}</span>
                </div>
            @endif

            <x-card.form
                class="admin-login__form"
                :action="route('admin.login.store')"
                :submit="['title' => 'ورود به پنل', 'type' => 'primary']"
            >
                <x-form.input
                    name="email"
                    type="email"
                    label="ایمیل مدیر"
                    placeholder="name@example.com"
                    autocomplete="username"
                    dir="ltr"
                    required
                    autofocus
                />
                <x-form.input
                    name="password"
                    type="password"
                    label="رمز عبور"
                    placeholder="رمز عبور خود را وارد کنید"
                    autocomplete="current-password"
                    dir="ltr"
                    required
                />
            </x-card.form>

            <div class="admin-login__secure-note">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3 19 6v5c0 4.42-2.99 8.56-7 10-4.01-1.44-7-5.58-7-10V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="m9 12 2 2 4-4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>این بخش فقط برای مدیران فروشگاه در دسترس است.</span>
            </div>

            <a href="{{ route('home') }}" class="admin-login__back-link">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M19 12H5m0 0 6 6m-6-6 6-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                بازگشت به فروشگاه
            </a>
        </section>

        <aside class="admin-login__visual" aria-label="معرفی پنل مدیریت {{ site_name() }}">
            <div class="admin-login__visual-glow" aria-hidden="true"></div>
            <div class="admin-login__visual-top">
                <a href="{{ route('home') }}" class="admin-login__brand" aria-label="{{ site_name() }}، صفحه اصلی">
                    <span class="admin-login__mark" aria-hidden="true">{{ mb_substr(site_name(), 0, 1) }}</span>
                    <span>
                        <strong>{{ site_name() }}</strong>
                        <small>فروشگاه آنلاین</small>
                    </span>
                </a>
                <span class="admin-login__admin-tag">ADMIN PANEL</span>
            </div>

            <div class="admin-login__visual-copy">
                <span class="admin-login__visual-kicker">مدیریت، یک‌جا و ساده</span>
                <h2>کنترل فروشگاه<br>در دستان شماست.</h2>
                <p>محصولات، سفارش‌ها و محتوای فروشگاه را از یک فضای امن مدیریت کنید.</p>
            </div>

            <div class="admin-login__feature-list" aria-label="بخش‌های پنل">
                <span><i aria-hidden="true">✓</i> مدیریت محصولات</span>
                <span><i aria-hidden="true">✓</i> پیگیری سفارش‌ها</span>
                <span><i aria-hidden="true">✓</i> تنظیم محتوای فروشگاه</span>
            </div>

            <div class="admin-login__visual-footer">
                <span class="admin-login__status-dot" aria-hidden="true"></span>
                <span>ورود امن مدیران</span>
                <span class="admin-login__visual-version">KARIN SHOP</span>
            </div>
        </aside>
    </main>
@endsection
