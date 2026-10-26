@props([
    'id',
    'title'  => null,
    'size'   => 'md',
    'label'  => null,
])

@php
    $maxWidth = match($size) {
        'sm'  => 'max-w-sm',
        'lg'  => 'max-w-2xl',
        'xl'  => 'max-w-4xl',
        'full'=> 'max-w-full',
        default => 'max-w-lg',
    };
@endphp

{{-- "app-modal" (not "modal") avoids colliding with the legacy `.modal { display: none }` rule in styles.css, which otherwise wins the cascade over Tailwind's hidden/flex utilities and keeps this permanently hidden. --}}
<div
    id="{{ $id }}"
    role="dialog"
    aria-modal="true"
    @if($label) aria-label="{{ $label }}"
    @elseif($title || isset($header)) aria-labelledby="{{ $id }}_title"
    @else aria-label="{{ site_name() }}" @endif
    tabindex="-1"
    class="app-modal hidden fixed inset-0 z-50 items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-out"
    {{ $attributes->except(['id', 'class', 'role', 'tabindex', 'aria-modal', 'aria-label', 'aria-labelledby']) }}
>
    <div class="app-modal-dialog relative w-full {{ $maxWidth }} max-h-[90vh] flex flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-gray-950/10 dark:bg-gray-900 dark:ring-white/10 opacity-0 scale-95 translate-y-3 transition-all duration-300 ease-out">
        @if($title || isset($header))
            <div class="ui-modal-header flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4 dark:border-gray-800">
                @if(isset($header))
                    <div id="{{ $id }}_title">{{ $header }}</div>
                @else
                    <h3 id="{{ $id }}_title" class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
                @endif
                <button
                    type="button"
                    class="ui-icon-button shrink-0 text-gray-500 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300"
                    aria-label="{{ __('Close') }}"
                    data-app-modal-dismiss="{{ $id }}"
                >
                    <i class="ki-filled ki-cross text-sm"></i>
                </button>
            </div>
        @endif

        <div class="overflow-y-auto px-6 py-5">
            {{ $slot }}
        </div>

        @if(isset($footer))
            <div class="ui-modal-footer flex items-center justify-end gap-2.5 border-t border-gray-100 px-6 py-4 dark:border-gray-800">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
