<!doctype html>
@php
    $siteSettings = site_settings();
    $pageTitle = $title ?? $siteSettings?->meta_title ?? site_name();
    $pageDescription = $description ?? $siteSettings?->meta_description;
    $shareImage = $siteSettings?->og_image_path ? url(public_image_url($siteSettings->og_image_path)) : null;
@endphp
<html lang="{{ str_replace('_', '-', config('theme.language')) }}" dir="{{ config('theme.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f9fafb">
    <title>{{ $pageTitle }}</title>
    @if ($pageDescription)<meta name="description" content="{{ $pageDescription }}">@endif
    <meta name="robots" content="{{ ($siteSettings?->robots_index ?? true) ? 'index' : 'noindex' }},{{ ($siteSettings?->robots_follow ?? true) ? 'follow' : 'nofollow' }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fa_IR">
    <meta property="og:site_name" content="{{ site_name() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    @if ($pageDescription)<meta property="og:description" content="{{ $pageDescription }}">@endif
    @if ($shareImage)<meta property="og:image" content="{{ $shareImage }}">@endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="{{ $shareImage ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    @if ($pageDescription)<meta name="twitter:description" content="{{ $pageDescription }}">@endif
    @if ($shareImage)<meta name="twitter:image" content="{{ $shareImage }}">@endif
    @if ($siteSettings?->google_site_verification)<meta name="google-site-verification" content="{{ $siteSettings->google_site_verification }}">@endif
    @if ($siteSettings?->bing_site_verification)<meta name="msvalidate.01" content="{{ $siteSettings->bing_site_verification }}">@endif
    @if ($siteSettings?->favicon_path)<link rel="icon" href="{{ public_image_url($siteSettings->favicon_path) }}">@endif

    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', savedTheme === 'dark' || (!savedTheme && prefersDark));
        })();
    </script>

    <link rel="stylesheet" href="{{ asset('assets/karin/styles/app.css') }}">
    <link rel="stylesheet" href="{{ versioned_assets('assets/karin/styles/user-layout.css') }}">
    <link rel="stylesheet" href="{{ versioned_assets('assets/karin/styles/store-footer.css') }}">
    <link href="{{ versioned_assets('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
    @livewireStyles
    @stack('styles')
    @vite(['resources/js/site.js'])
</head>
<body class="user-site">
    <a class="user-skip-link" href="#main-content">رفتن به محتوای اصلی</a>

    @yield('content')

    @stack('scripts')
    @livewireScripts
</body>
</html>
