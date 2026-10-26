@php
    $isOwner = request()->is('owner*');
    $authUser = $isOwner ? auth()->user() : auth('admin')->user();
    $logoutRouteName = $isOwner ? 'owner.auth.logout' : 'admin.auth.logout';
    $userName = $authUser?->first_name
        ? trim($authUser->first_name.' '.($authUser->last_name ?? ''))
        : ($authUser?->name ?? 'حساب کاربری');
@endphp

<div class="border-t border-slate-800 p-4" id="sidebar_footer">
    <div class="flex items-center gap-3 rounded-xl bg-slate-900 p-3">
        <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-indigo-400/15 text-sm font-bold text-indigo-300" aria-hidden="true">
            {{ mb_substr($userName, 0, 1) }}
        </span>
        <div class="min-w-0 flex-1">
            <span class="block truncate text-sm font-semibold text-slate-100">{{ $userName }}</span>
            <span class="block truncate text-xs text-slate-400" dir="ltr">{{ $authUser?->phone ?? $authUser?->email ?? '' }}</span>
        </div>
        @if (\Illuminate\Support\Facades\Route::has($logoutRouteName))
            <form method="POST" action="{{ route($logoutRouteName) }}">
                @csrf
                <button type="submit" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-800 hover:text-rose-300" aria-label="خروج از حساب" title="خروج">
                    <i class="ki-filled ki-exit-right" aria-hidden="true"></i>
                </button>
            </form>
        @endif
    </div>
</div>
