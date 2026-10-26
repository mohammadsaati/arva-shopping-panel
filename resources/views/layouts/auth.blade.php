<!DOCTYPE html>
<html class="min-h-full"
      data-theme="true"
      data-theme-mode="{{ config('theme.mode') }}"
      dir="{{ config('theme.direction') }}"
      lang="{{ config('theme.language') }}"
>
<head>
    <title>{{ site_name() }}</title>

    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (site_settings()?->favicon_path)<link rel="icon" href="{{ public_image_url(site_settings()->favicon_path) }}">@endif

    <link href="{{ versioned_assets('assets/fonts/iransans/font.css') }}" rel="stylesheet"/>

    @vite(['resources/js/theme.js'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="{{ versioned_assets('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
</head>
<body class="admin-auth-shell antialiased flex min-h-screen items-center justify-center text-base text-gray-700">

    <div class="w-full max-w-[1160px] px-4 py-8 sm:px-6 lg:py-12">
        @yield('content')
    </div>

    <script src="{{ versioned_assets('assets/js/core.bundle.js') }}"></script>
</body>
</html>
