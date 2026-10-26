<!DOCTYPE html>
<html lang="{{ config('theme.language') }}" dir="rtl" class="motion-safe:scroll-smooth">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? site_name() }}</title>
    @if (site_settings()?->favicon_path)<link rel="icon" href="{{ public_image_url(site_settings()->favicon_path) }}">@endif

    <link href="{{ versioned_assets('assets/fonts/iransans/font.css') }}" rel="stylesheet"/>
    @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="bg-site-bg text-site-ink antialiased overflow-x-hidden [font-feature-settings:'ss01']">

    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:start-4 focus:top-4 focus:z-50 focus:rounded-xl focus:bg-white focus:px-5 focus:py-3 focus:text-site-primary focus:shadow-lg">رفتن به محتوای اصلی</a>
    @yield('content')

</body>
</html>
