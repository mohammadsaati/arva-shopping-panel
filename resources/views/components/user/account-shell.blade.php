@props([
    'title',
    'subTitle' => null,
    'active' => 'profile',
])

<x-user-panel :title="$title" :sub-title="$subTitle">
    @isset($actions)
        <x-slot:actions>{{ $actions }}</x-slot:actions>
    @endisset

    <div class="user-account-shell">
        <aside class="user-account-sidebar" aria-label="منوی حساب کاربری">
            <div class="user-account-sidebar__identity">
                <span class="user-account-sidebar__avatar" aria-hidden="true"><i class="ki-filled ki-profile-circle"></i></span>
                <div>
                    <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong>
                    <span dir="ltr">{{ auth()->user()->phone }}</span>
                </div>
            </div>

            <nav class="user-account-nav" aria-label="بخش‌های حساب">
                <a href="{{ route('account.show') }}" @class(['user-account-nav__link', 'is-active' => $active === 'profile']) @if($active === 'profile') aria-current="page" @endif>
                    <i class="ki-filled ki-user" aria-hidden="true"></i><span>اطلاعات حساب</span>
                </a>
                <a href="{{ route('account.addresses.index') }}" @class(['user-account-nav__link', 'is-active' => $active === 'addresses']) @if($active === 'addresses') aria-current="page" @endif>
                    <i class="ki-filled ki-geolocation" aria-hidden="true"></i><span>نشانی‌های من</span>
                </a>
                <a href="{{ route('account.orders.index') }}" @class(['user-account-nav__link', 'is-active' => $active === 'orders']) @if($active === 'orders') aria-current="page" @endif>
                    <i class="ki-filled ki-basket" aria-hidden="true"></i><span>سفارش‌های من</span>
                </a>
            </nav>

            <div class="user-account-sidebar__logout">
                <x-card.form :action="route('logout')" :submit="['title' => 'خروج از حساب', 'type' => 'danger']" />
            </div>
        </aside>

        <div class="user-account-shell__content">
            {{ $slot }}
        </div>
    </div>
</x-user-panel>
