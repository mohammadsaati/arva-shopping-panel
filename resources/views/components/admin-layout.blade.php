<!DOCTYPE html>
<html class="h-full"
      data-theme="true"
      data-theme-mode="{{ config('theme.mode') }}"
      dir="{{ config('theme.direction') }}"
      lang="{{ config('theme.language') }}"
>
<head>
    <title>{{ site_name() }} | پنل مدیریت</title>

    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (site_settings()?->favicon_path)<link rel="icon" href="{{ public_image_url(site_settings()->favicon_path) }}">@endif
    <meta content="assets/media/app/og-image.png" property="og:image"/>

    <link href="{{ versioned_assets('assets/fonts/iransans/font.css') }}" rel="stylesheet"/>

    @vite(['resources/js/theme.js'])
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="{{ versioned_assets('assets/vendors/keenicons/styles.bundle.css') }}" rel="stylesheet"/>
    <link href="{{ versioned_assets('assets/vendors/apexcharts/apexcharts.css') }}" rel="stylesheet"/>
</head>
<body
    class="panel-shell antialiased flex h-full text-base text-gray-700 [--tw-page-bg:var(--tw-coal-300)] [--tw-content-bg:var(--tw-light)] [--tw-content-bg-dark:var(--tw-coal-500)] [--tw-content-scrollbar-color:#e8e8e8] [--tw-header-height:60px] [--tw-sidebar-width:270px] bg-[--tw-page-bg] lg:overflow-hidden">
<div class="flex grow">
    @include('partials.layouts.header-mobile')

    <div class="flex flex-col lg:flex-row grow pt-[--tw-header-height] lg:pt-0">
        @include('partials.layouts.side-bar')

        <!-- Main -->
        <div class="panel-content flex flex-col grow lg:rounded-s-2xl lg:ms-[--tw-sidebar-width]">
            <div class="flex flex-col grow lg:scrollable-y-auto lg:[scrollbar-width:auto] lg:light:[--tw-scrollbar-thumb-color:var(--tw-content-scrollbar-color)] pt-5" id="scrollable_content">
                @yield('content', '')

                @include('partials.layouts.footer')
            </div>
        </div>
        <!-- End of Main -->
    </div>


</div>


</body>

<script src="{{ versioned_assets('assets/js/core.bundle.js') }}"></script>
<script src="{{ versioned_assets('assets/js/widgets/general.js') }}"></script>
<script src="{{ versioned_assets('assets/js/layouts/demo1.js') }}"></script>
@stack('scripts')

</html>
