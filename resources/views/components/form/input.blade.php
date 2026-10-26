@props([
    'name',
    'type'        => 'text',
    'value'       => null,
    'placeholder' => null,
    'label'       => null,
    'hint'        => null,
    'disabled'    => false,
    'readonly'    => false,
    'required'    => false,
    'prefix'      => null,
    'suffix'      => null,
    'icon'        => null,
    'revealable'  => false,
    'class'       => '',
])

@php
    $fieldKey = preg_replace('/\[([^\]]+)\]/', '.$1', $name);
    $hasError = $errors->has($fieldKey);
    $errorMsg = $hasError ? $errors->first($fieldKey) : null;
    $inputId  = $attributes->get('id') ?? 'input_' . str_replace(['.', '[', ']'], '_', $name);
    $oldValue = old($fieldKey, $value);
@endphp

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="input {{ $prefix || $suffix || $icon || ($revealable && $type === 'password') ? 'flex items-center gap-2' : '' }} {{ $hasError ? 'border-danger' : '' }}">
        @if($icon)
            <i class="{{ $icon }} ui-input-icon" aria-hidden="true"></i>
        @endif
        @if($prefix)
            <span class="text-gray-500 shrink-0">{{ $prefix }}</span>
        @endif

        <input
            id="{{ $inputId }}"
            aria-invalid="{{ $hasError ? 'true' : 'false' }}"
            @if($errorMsg || $hint || $attributes->get('aria-describedby'))
                aria-describedby="{{ trim(($attributes->get('aria-describedby') ?? '') . ' ' . (($errorMsg || $hint) ? $inputId . '_feedback' : '')) }}"
            @endif
            type="{{ $type }}"
            name="{{ $name }}"
            @if($oldValue !== null) value="{{ $oldValue }}" @endif
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
            @if($required) required @endif
            class="grow bg-transparent outline-none w-full"
            {{ $attributes->except(['id', 'aria-invalid', 'aria-describedby', 'name', 'type', 'value', 'placeholder', 'disabled', 'readonly', 'required', 'class']) }}
        />

        @if($suffix)
            <span class="text-gray-500 shrink-0">{{ $suffix }}</span>
        @endif

        @if($revealable && $type === 'password')
            <button
                type="button"
                class="ui-input-reveal"
                data-password-visibility-toggle
                aria-controls="{{ $inputId }}"
                aria-label="نمایش رمز عبور"
                aria-pressed="false"
            >نمایش</button>
        @endif
    </div>

    @if($errorMsg)
        <p id="{{ $inputId }}_feedback" class="text-danger text-xs mt-0.5" role="alert">{{ $errorMsg }}</p>
    @elseif($hint)
        <p id="{{ $inputId }}_feedback" class="form-hint">{{ $hint }}</p>
    @endif
</div>
