@props([
    'type'     => 'button',
    'variant'  => 'primary',
    'size'     => 'md',
    'disabled' => false,
    'href'     => null,
    'loading'  => false,
    'icon'     => null,
    'class'    => '',
])

@php
    $sizeClass = match($size) {
        'sm'  => 'btn-sm',
        'lg'  => 'btn-lg',
        default => '',
    };
    $baseClass = trim("btn btn-{$variant} {$sizeClass} inline-flex items-center gap-2 {$class}");
@endphp

@if($href)
    <a
        @if(! $disabled && ! $loading) href="{{ $href }}" @endif
        @if($disabled || $loading) role="link" aria-disabled="true" tabindex="-1" @endif
        @if($loading) aria-busy="true" @endif
        class="{{ $baseClass }} {{ $disabled || $loading ? 'opacity-50 pointer-events-none' : '' }}"
        {{ $attributes->except(['class', 'href']) }}
    >
        @if($icon && ! $loading) <i class="{{ $icon }}" aria-hidden="true"></i> @endif
        @if($loading)
            <span aria-hidden="true" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
        @endif
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        class="{{ $baseClass }}"
        @if($disabled || $loading) disabled @endif
        @if($loading) aria-busy="true" @endif
        {{ $attributes->except(['class', 'type']) }}
    >
        @if($icon && ! $loading) <i class="{{ $icon }}" aria-hidden="true"></i> @endif
        @if($loading)
            <span aria-hidden="true" class="inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
        @endif
        {{ $slot }}
    </button>
@endif
