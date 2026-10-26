@props([
    'name',
    'options'     => [],
    'selected'    => null,
    'placeholder' => null,
    'label'       => null,
    'hint'        => null,
    'disabled'    => false,
    'required'    => false,
    'multiple'    => false,
    'class'       => '',
])

@php
    $fieldKey   = preg_replace('/\[([^\]]+)\]/', '.$1', $name);
    $hasError   = $errors->has($fieldKey);
    $errorMsg   = $hasError ? $errors->first($fieldKey) : null;
    $inputId    = $attributes->get('id') ?? 'select_' . str_replace(['.', '[', ']'], '_', $name);
    $oldValue   = old($fieldKey, $selected);
    $fieldName  = $multiple ? $name . '[]' : $name;
@endphp

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <select
        id="{{ $inputId }}"
        aria-invalid="{{ $hasError ? 'true' : 'false' }}"
        @if($errorMsg || $hint || $attributes->get('aria-describedby'))
            aria-describedby="{{ trim(($attributes->get('aria-describedby') ?? '') . ' ' . (($errorMsg || $hint) ? $inputId . '_feedback' : '')) }}"
        @endif
        name="{{ $fieldName }}"
        @if($disabled) disabled @endif
        @if($required) required @endif
        @if($multiple) multiple @endif
        class="select {{ $hasError ? 'border-danger' : '' }}"
        {{ $attributes->except(['id', 'aria-invalid', 'aria-describedby', 'name', 'disabled', 'required', 'multiple', 'class']) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optValue => $optLabel)
            @if(is_array($optLabel))
                {{-- Option group --}}
                <optgroup label="{{ $optValue }}">
                    @foreach($optLabel as $groupValue => $groupLabel)
                        <option
                            value="{{ $groupValue }}"
                            @if((string) $groupValue === (string) $oldValue) selected @endif
                        >{{ $groupLabel }}</option>
                    @endforeach
                </optgroup>
            @else
                <option
                    value="{{ $optValue }}"
                    @if(
                        is_array($oldValue)
                            ? in_array((string) $optValue, array_map('strval', $oldValue))
                            : (string) $optValue === (string) $oldValue
                    ) selected @endif
                >{{ $optLabel }}</option>
            @endif
        @endforeach

        {{ $slot ?? '' }}
    </select>

    @if($errorMsg)
        <p id="{{ $inputId }}_feedback" class="text-danger text-xs mt-0.5" role="alert">{{ $errorMsg }}</p>
    @elseif($hint)
        <p id="{{ $inputId }}_feedback" class="form-hint">{{ $hint }}</p>
    @endif
</div>
