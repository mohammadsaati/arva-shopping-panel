@props([
    'name',
    'options'  => [],
    'selected' => null,
    'label'    => null,
    'disabled' => false,
    'inline'   => false,
    'hint'     => null,
    'class'    => '',
])

@php
    $oldValue = old($name, $selected);
@endphp

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    @if($label)
        <span class="form-label">{{ $label }}</span>
    @endif

    <div class="{{ $inline ? 'flex flex-wrap gap-4' : 'flex flex-col gap-2' }}">
        @foreach($options as $optValue => $optLabel)
            @php $radioId = 'radio_' . str_replace(['.', '[', ']'], '_', $name) . '_' . $optValue; @endphp
            <label class="flex items-center gap-2 cursor-pointer {{ $disabled ? 'opacity-50 cursor-not-allowed' : '' }}">
                <input
                    type="radio"
                    id="{{ $radioId }}"
                    name="{{ $name }}"
                    value="{{ $optValue }}"
                    @if((string) $oldValue === (string) $optValue) checked @endif
                    @if($disabled) disabled @endif
                    class="radio"
                />
                <span class="radio-label">{{ $optLabel }}</span>
            </label>
        @endforeach
    </div>

    @if($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif

    @error($name)
        <p class="text-danger text-xs mt-0.5">{{ $message }}</p>
    @enderror
</div>
