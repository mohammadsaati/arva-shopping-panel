@props([
    'title',
    'value',
    'icon'    => null,
    'change'  => null,
    'trend'   => null,
    'prefix'  => null,
    'suffix'  => null,
    'color'   => 'primary',
    'class'   => '',
])

@php
    $trendColor = match($trend) {
        'up'   => 'text-success',
        'down' => 'text-danger',
        default => 'text-gray-500',
    };
    $trendIcon = match($trend) {
        'up'   => 'ki-filled ki-arrow-up',
        'down' => 'ki-filled ki-arrow-down',
        default => null,
    };
@endphp

<div class="card ui-stat {{ $class }}" data-color="{{ $color }}" {{ $attributes->except(['class', 'data-color']) }}>
    <div class="card-body flex items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <p class="ui-stat-label">{{ $title }}</p>
            <p class="ui-stat-value">
                @if($prefix) <span class="text-lg font-medium text-gray-400">{{ $prefix }}</span> @endif
                {{ $value }}
                @if($suffix) <span class="text-lg font-medium text-gray-400">{{ $suffix }}</span> @endif
            </p>
            @if($change !== null)
                <p class="flex items-center gap-1 text-xs font-medium {{ $trendColor }}">
                    @if($trendIcon) <i class="{{ $trendIcon }}"></i> @endif
                    {{ $change }}
                </p>
            @endif
        </div>

        @if($icon)
            <div class="ui-stat-icon" aria-hidden="true">
                <i class="{{ $icon }} text-2xl"></i>
            </div>
        @endif
    </div>
</div>
