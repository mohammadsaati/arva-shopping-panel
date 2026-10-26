@props([
    'label',
    'for' => null,
    'wide' => false,
])

<div {{ $attributes->class([
    'flex flex-col ui-filter-field',
    'ui-filter-field--wide' => $wide,
]) }}>
    <label @if ($for) for="{{ $for }}" @endif>{{ $label }}</label>
    {{ $slot }}
</div>
