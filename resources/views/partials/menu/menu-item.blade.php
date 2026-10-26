{{-- Recursive menu item partial. Call via @include('partials.menu.menu-item', ['item' => $item, 'depth' => 0]) --}}
@php
    /** @var \App\Commons\Menu\MenuItem $item */
    $badge = $item->getBadge();
    $badgeValue = $badge?->getValue();
    $badgeColorMap = [
        'primary' => 'bg-violet-500 text-white',
        'success' => 'bg-green-500 text-white',
        'danger'  => 'bg-red-500 text-white',
        'warning' => 'bg-yellow-400 text-gray-900',
        'info'    => 'bg-blue-500 text-white',
        'dark'    => 'bg-gray-700 text-white',
    ];
    $badgeClass = $badgeColorMap[$badge?->getColor() ?? 'primary'] ?? $badgeColorMap['primary'];
@endphp

@if($item->hasChildren())
    <div class="menu-item {{ $item->isActive() ? 'here show' : '' }}"
         data-menu-item-toggle="accordion"
         data-menu-item-trigger="click">

        <div class="menu-link gap-2.5 py-2 px-2.5 rounded-md !menu-item-hover:bg-transparent !menu-item-here:bg-transparent">
            @if($item->getIcon() && $depth === 0)
                <span class="menu-icon items-start text-gray-400 text-lg menu-item-here:text-gray-900 menu-item-show:text-gray-900 menu-link-hover:text-gray-900">
                    <i class="ki-filled {{ $item->getIcon() }}"></i>
                </span>
            @endif
            <span class="{{ $depth === 0 ? 'menu-title font-medium text-sm text-gray-700' : 'menu-title text-2sm text-gray-800' }} menu-item-here:text-gray-900 menu-item-show:text-gray-900 menu-link-hover:text-gray-900">
                {{ $item->getTitle() }}
            </span>
            @if($badge !== null)
                <span @if($item->getRouteName()) data-menu-badge="{{ $item->getRouteName() }}" @endif class="ml-auto text-xs font-semibold leading-none px-1.5 py-0.5 rounded-full {{ $badgeClass }} {{ $badgeValue === null ? 'hidden' : '' }}">
                    {{ $badgeValue }}
                </span>
            @endif
            <span class="menu-arrow {{ $badgeValue !== null ? '' : 'ml-auto' }} text-gray-400 menu-item-here:text-gray-400 menu-item-show:text-gray-800 menu-link-hover:text-gray-800">
                <i class="ki-filled ki-down text-3xs menu-item-show:hidden"></i>
                <i class="ki-filled ki-up text-3xs hidden menu-item-show:inline-flex"></i>
            </span>
        </div>

        <div class="menu-accordion gap-px {{ $depth === 0 ? 'ps-7' : 'ps-2.5' }}">
            @foreach($item->getVisibleSubMenus() as $child)
                @include('partials.menu.menu-item', ['item' => $child, 'depth' => $depth + 1])
            @endforeach
        </div>
    </div>
@else
    <div class="menu-item {{ $item->isActive() ? 'active' : '' }}">
        <a class="menu-link {{ $depth === 0 ? 'gap-2.5' : '' }} py-2 px-2.5 rounded-md menu-item-active:bg-gray-100 menu-link-hover:bg-gray-100"
           href="{{ $item->getUrl() }}">
            @if($item->getIcon() && $depth === 0)
                <span class="menu-icon items-start text-lg text-gray-600 menu-item-active:text-gray-900 menu-item-here:text-gray-900">
                    <i class="ki-filled {{ $item->getIcon() }}"></i>
                </span>
            @endif
            <span class="{{ $depth === 0 ? 'menu-title text-sm font-medium text-gray-800' : 'menu-title text-2sm text-gray-800 menu-item-active:font-medium' }} menu-item-here:text-gray-900 menu-item-active:text-gray-900 menu-link-hover:text-gray-900">
                {{ $item->getTitle() }}
            </span>
            @if($badge !== null)
                <span @if($item->getRouteName()) data-menu-badge="{{ $item->getRouteName() }}" @endif class="ml-auto text-xs font-semibold leading-none px-1.5 py-0.5 rounded-full {{ $badgeClass }} {{ $badgeValue === null ? 'hidden' : '' }}">
                    {{ $badgeValue }}
                </span>
            @endif
        </a>
    </div>
@endif
