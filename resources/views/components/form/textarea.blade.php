@props([
    'name',
    'value'       => null,
    'placeholder' => null,
    'label'       => null,
    'hint'        => null,
    'rows'        => 4,
    'disabled'    => false,
    'readonly'    => false,
    'required'    => false,
    'class'       => '',
])

@php
    $fieldKey = preg_replace('/\[([^\]]+)\]/', '.$1', $name);
    $hasError = $errors->has($fieldKey);
    $errorMsg = $hasError ? $errors->first($fieldKey) : null;
    $inputId  = $attributes->get('id') ?? 'textarea_' . str_replace(['.', '[', ']'], '_', $name);
    $oldValue = old($fieldKey, $value);
@endphp

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <textarea
        id="{{ $inputId }}"
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        @if($errorMsg || $hint || $attributes->get('aria-describedby'))
            aria-describedby="{{ trim(($attributes->get('aria-describedby') ?? '') . ' ' . (($errorMsg || $hint) ? $inputId . '_feedback' : '')) }}"
        @endif
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($disabled) disabled @endif
        @if($readonly) readonly @endif
        @if($required) required @endif
        class="textarea resize-y {{ $hasError ? 'border-danger' : '' }}"
        {{ $attributes->except(['id', 'aria-invalid', 'aria-describedby', 'name', 'value', 'placeholder', 'disabled', 'readonly', 'required', 'rows', 'class']) }}
    >{{ $oldValue }}</textarea>

    @if($errorMsg)
        <p id="{{ $inputId }}_feedback" class="text-danger text-xs mt-0.5" role="alert">{{ $errorMsg }}</p>
    @elseif($hint)
        <p id="{{ $inputId }}_feedback" class="form-hint">{{ $hint }}</p>
    @endif
</div>
