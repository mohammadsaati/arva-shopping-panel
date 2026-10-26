@props([
    'name',
    'value'    => '1',
    'label'    => null,
    'checked'  => false,
    'disabled' => false,
    'size'     => 'md',
    'hint'     => null,
    'class'    => '',
])

@php
    $inputId   = 'toggle_' . str_replace(['.', '[', ']'], '_', $name);
    $isChecked = old($name, $checked);
    $sizeClass = match($size) {
        'sm' => 'switch-sm',
        'lg' => 'switch-lg',
        default => '',
    };
@endphp

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    <label class="switch {{ $sizeClass }} {{ $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer' }}">
        <input
            type="checkbox"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $value }}"
            @if($isChecked) checked @endif
            @if($disabled) disabled @endif
            {{ $attributes->except(['name', 'value', 'checked', 'disabled', 'class']) }}
        />
        @if($label)
            <span class="switch-label">{{ $label }}</span>
        @endif
    </label>

    @if($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-danger text-xs mt-0.5">{{ $message }}</p>
    @enderror
</div>
