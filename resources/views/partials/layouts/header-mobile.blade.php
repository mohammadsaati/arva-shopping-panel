@php
    $isOwner = request()->is('owner*');
    $dashboardRoute = $isOwner ? 'owner.dashboard.index' : 'admin.dashboard.index';
    $dashboardUrl = \Illuminate\Support\Facades\Route::has($dashboardRoute) ? route($dashboardRoute) : url('/admin');
    $title = config('theme.title', config('app.name', 'فروشگاه کلوهی'));
@endphp

<header class="flex items-center justify-between gap-3 border-b border-slate-200 bg-white px-5 py-4 lg:hidden" id="header">
    <div>
        <a class="text-sm font-bold text-slate-900" href="{{ $dashboardUrl }}">{{ $title }}</a>
        <p class="mt-0.5 text-xs text-slate-500">{{ $isOwner ? 'پنل مدیریت سالن' : 'پنل مدیریت' }}</p>
    </div>
    <button type="button" class="rounded-xl border border-slate-200 p-2 text-slate-700" data-admin-sidebar-toggle aria-expanded="false" aria-controls="sidebar" aria-label="باز کردن منو">
        <i class="ki-filled ki-menu text-lg" aria-hidden="true"></i>
    </button>
</header>
