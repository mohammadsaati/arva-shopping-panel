@props([
    'mode' => 'login',
    'title',
    'subtitle',
    'welcomeTitle',
    'welcomeText',
])

@php
    $fromCheckout = str_contains((string) session('url.intended', ''), '/checkout');
@endphp

<section class="user-auth-stage" data-auth-stage="{{ $mode }}">
    <div class="user-auth-stage__sky" aria-hidden="true">
        <span class="user-auth-orb user-auth-orb--a"></span>
        <span class="user-auth-orb user-auth-orb--b"></span>
        <span class="user-auth-orb user-auth-orb--c"></span>
        <span class="user-auth-spark user-auth-spark--a"></span>
        <span class="user-auth-spark user-auth-spark--b"></span>
        <span class="user-auth-spark user-auth-spark--c"></span>
    </div>

    <aside class="user-auth-welcome">
        <p class="user-auth-welcome__mark">{{ site_name() }}</p>
        <h2>{{ $welcomeTitle }}</h2>
        <p>{{ $welcomeText }}</p>
        <ul>
            <li><i class="ki-filled ki-shield-tick" aria-hidden="true"></i> پرداخت امن</li>
            <li><i class="ki-filled ki-delivery" aria-hidden="true"></i> پیگیری سفارش</li>
            <li><i class="ki-filled ki-basket" aria-hidden="true"></i> سبد ذخیره‌شده</li>
        </ul>
    </aside>

    <div @class(['user-auth-card', 'user-auth-card--register' => $mode === 'register'])>
        <button class="user-auth-theme-toggle" type="button" data-theme-toggle aria-label="تغییر پوسته" aria-pressed="false">
            <svg class="dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.5 15.2A8.5 8.5 0 0 1 8.8 3.5 8.5 8.5 0 1 0 20.5 15.2Z" />
            </svg>
            <svg class="hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <circle cx="12" cy="12" r="4" />
                <path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42" />
            </svg>
        </button>

        <a class="user-auth-brand" href="{{ route('home') }}" aria-label="{{ site_name() }} - صفحه اصلی">
            <span>{{ site_name() }}</span>
        </a>

        <nav class="user-auth-tabs" aria-label="ورود یا ثبت‌نام">
            <a href="{{ route('login') }}" @class(['is-on' => $mode === 'login'])>ورود</a>
            <a href="{{ route('register') }}" @class(['is-on' => $mode === 'register'])>ثبت‌نام</a>
        </nav>

        <div class="user-auth-copy">
            <h1>{{ $title }}</h1>
            <p>{{ $subtitle }}</p>
        </div>

        @if ($fromCheckout)
            <p class="user-auth-intent" role="status">بعد از این مرحله، برمی‌گردی سر تکمیل سفارش.</p>
        @endif

        {{ $slot }}
    </div>
</section>
