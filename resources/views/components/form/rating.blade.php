@props([
    'name',
    'options' => [],
    'selected' => null,
    'label' => null,
    'required' => false,
])

@php
    $oldValue = old($name, $selected);
    $errorMessage = $errors->first($name);
    $fieldId = 'rating_' . str_replace(['.', '[', ']'], '_', $name);
@endphp

<fieldset class="product-review-rating" @if($errorMessage) aria-describedby="{{ $fieldId }}_error" @endif>
    @if($label)
        <legend class="product-review-rating__legend">{{ $label }} @if($required)<span aria-hidden="true">*</span>@endif</legend>
    @endif
    <div class="product-review-rating__options">
        @foreach($options as $value => $option)
            <div class="product-review-rating__choice">
                <input
                    type="radio"
                    id="{{ $fieldId }}_{{ $value }}"
                    name="{{ $name }}"
                    value="{{ $value }}"
                    @checked((string) $oldValue === (string) $value)
                    @if($required) required @endif
                >
                <label for="{{ $fieldId }}_{{ $value }}">
                    <span class="product-review-rating__emoji" aria-hidden="true">{{ $option['emoji'] }}</span>
                    <span>{{ $option['label'] }}</span>
                </label>
            </div>
        @endforeach
    </div>
    @if($errorMessage)
        <p class="product-review-rating__error" id="{{ $fieldId }}_error" role="alert">{{ $errorMessage }}</p>
    @endif
</fieldset>
