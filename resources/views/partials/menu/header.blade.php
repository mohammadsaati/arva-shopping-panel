@php
    $isOwner = request()->is('owner*');
    $dashboardRoute = $isOwner ? 'owner.dashboard.index' : 'admin.dashboard.index';
    $dashboardUrl = \Illuminate\Support\Facades\Route::has($dashboardRoute) ? route($dashboardRoute) : url('/admin');
    $title = config('theme.title', config('app.name', 'فروشگاه کلوهی'));
@endphp

<div class="border-b border-slate-800 px-5 py-5" id="sidebar_header">
    <a class="flex items-center gap-3" href="{{ $dashboardUrl }}">
        <span class="flex size-11 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-black text-white" aria-hidden="true">C</span>
        <span class="flex flex-col">
            <span class="font-bold tracking-tight text-slate-100">{{ $title }}</span>
            <span class="mt-0.5 text-xs text-slate-400">{{ $isOwner ? 'پنل مدیریت سالن' : 'پنل مدیریت' }}</span>
        </span>
    </a>
</div>
