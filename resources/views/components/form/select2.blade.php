@props([
    'name',
    'options' => [],
    'selected' => null,
    'placeholder' => 'انتخاب کنید',
    'label' => null,
    'hint' => null,
    'disabled' => false,
    'required' => false,
    'multiple' => false,
    'searchable' => true,
    'clearable' => true,
    'route' => null,
    'class' => '',
])

@php
    $fieldKey = preg_replace('/\[([^\]]+)\]/', '.$1', $name);
    $hasError = $errors->has($fieldKey);
    $errorMsg = $hasError ? $errors->first($fieldKey) : null;
    $inputId = $attributes->get('id') ?? 'select2_' . str_replace(['.', '[', ']'], '_', $name);
    $oldValue = old($fieldKey, $selected);
    $selectedValues = array_map('strval', is_array($oldValue) ? $oldValue : ($oldValue === null || $oldValue === '' ? [] : [$oldValue]));
    $fieldName = $multiple ? $name . '[]' : $name;
    $optionValues = [];
    foreach ($options as $optionValue => $optionLabel) {
        if (is_array($optionLabel)) {
            foreach ($optionLabel as $groupValue => $_groupLabel) {
                $optionValues[] = (string) $groupValue;
            }
        } else {
            $optionValues[] = (string) $optionValue;
        }
    }
@endphp

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    @if($label)
        <label id="{{ $inputId }}_label" for="{{ $inputId }}" data-select2-label data-select2-for="{{ $inputId }}_trigger" class="form-label">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div
        class="ui-select2 {{ $disabled ? 'is-disabled' : '' }} {{ $hasError ? 'has-error' : '' }}"
        data-select2
        data-searchable="{{ $searchable ? 'true' : 'false' }}"
        data-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-clearable="{{ $clearable ? 'true' : 'false' }}"
        data-placeholder="{{ $placeholder }}"
        data-remote-url="{{ $route }}"
    >
        <select
            id="{{ $inputId }}"
            name="{{ $fieldName }}"
            class="select ui-select2__native"
            data-select2-native
            tabindex="-1"
            aria-hidden="true"
            @if($disabled) disabled @endif
            @if($required) required @endif
            @if($multiple) multiple @endif
            @if($hasError) aria-invalid="true" @endif
            {{ $attributes->except(['id', 'name', 'class', 'disabled', 'required', 'multiple', 'aria-invalid', 'aria-describedby']) }}
        >
            @if($placeholder && ! $multiple)
                <option value="" data-select2-placeholder>{{ $placeholder }}</option>
            @endif

            @foreach($options as $optValue => $optLabel)
                @if(is_array($optLabel))
                    <optgroup label="{{ $optValue }}">
                        @foreach($optLabel as $groupValue => $groupLabel)
                            <option value="{{ $groupValue }}" @selected(in_array((string) $groupValue, $selectedValues, true))>
                                {{ $groupLabel }}
                            </option>
                        @endforeach
                    </optgroup>
                @else
                    <option value="{{ $optValue }}" @selected(in_array((string) $optValue, $selectedValues, true))>
                        {{ $optLabel }}
                    </option>
                @endif
            @endforeach

            @if($route)
                @foreach(array_diff($selectedValues, $optionValues) as $selectedValue)
                    <option value="{{ $selectedValue }}" data-select2-pending selected></option>
                @endforeach
            @endif

            {{ $slot ?? '' }}
        </select>

        <div class="ui-select2__control {{ $hasError ? 'border-danger' : '' }}">
            <div
                id="{{ $inputId }}_trigger"
                class="ui-select2__trigger"
                data-select2-trigger
                role="combobox"
                tabindex="{{ $disabled ? '-1' : '0' }}"
                aria-haspopup="listbox"
                aria-expanded="false"
                aria-controls="{{ $inputId }}_options"
                @if($label)
                    aria-labelledby="{{ $inputId }}_label"
                @else
                    aria-label="{{ $placeholder ?: 'انتخاب کنید' }}"
                @endif
                aria-required="{{ $required ? 'true' : 'false' }}"
                aria-invalid="{{ $hasError ? 'true' : 'false' }}"
                aria-disabled="{{ $disabled ? 'true' : 'false' }}"
                @if($errorMsg || $hint || $attributes->get('aria-describedby'))
                    aria-describedby="{{ trim(($attributes->get('aria-describedby') ?? '') . ' ' . (($errorMsg || $hint) ? $inputId . '_feedback' : '')) }}"
                @endif
            >
                <span class="ui-select2__value" data-select2-value></span>
            </div>

            <div class="ui-select2__actions">
                @if($clearable)
                    <button
                        type="button"
                        class="ui-select2__clear"
                        data-select2-clear
                        aria-label="پاک کردن انتخاب"
                        title="پاک کردن انتخاب"
                        hidden
                    >×</button>
                @endif
                <span class="ui-select2__chevron" aria-hidden="true"></span>
            </div>
        </div>

        <div class="ui-select2__panel" data-select2-panel hidden>
            @if($searchable)
                <div class="ui-select2__search-wrap">
                    <input
                        type="search"
                        class="ui-select2__search"
                        data-select2-search
                        placeholder="جستجو..."
                        aria-label="جستجو در گزینه‌ها"
                        autocomplete="off"
                        tabindex="-1"
                    />
                </div>
            @endif
            <p class="ui-select2__loading" data-select2-loading hidden role="status">در حال دریافت گزینه‌ها...</p>
            <div
                id="{{ $inputId }}_options"
                class="ui-select2__options"
                data-select2-options
                role="listbox"
                aria-multiselectable="{{ $multiple ? 'true' : 'false' }}"
                tabindex="-1"
            ></div>
            <p class="ui-select2__empty" data-select2-empty hidden>گزینه‌ای پیدا نشد.</p>
        </div>
    </div>

    @if($errorMsg)
        <p id="{{ $inputId }}_feedback" class="text-danger text-xs mt-0.5" role="alert">{{ $errorMsg }}</p>
    @elseif($hint)
        <p id="{{ $inputId }}_feedback" class="form-hint">{{ $hint }}</p>
    @endif
</div>
