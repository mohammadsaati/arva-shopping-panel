@props([
    'name',
    'label'        => null,
    'hint'         => null,
    'accept'       => null,
    'multiple'     => false,
    'disabled'     => false,
    'required'     => false,
    'currentImage' => null,
    'class'        => '',
])

@php
    $errorName  = preg_replace('/\[(\w+)\]/', '.$1', $name);
    $hasError   = $errors->has($errorName);
    $errorMsg   = $hasError ? $errors->first($errorName) : null;
    $inputId    = $attributes->get('id') ?? 'file_' . str_replace(['.', '[', ']'], '_', $name);
    $fieldName  = $multiple ? $name . '[]' : $name;
    // A current image already satisfies the field, so it can't be required again.
    $isRequired = $required && !$currentImage;
@endphp

<div data-file-input class="ui-field flex flex-col gap-1.5 {{ $class }}">
    @if($label)
        <label for="{{ $inputId }}" class="form-label">
            {{ $label }}
            @if($isRequired) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="flex items-center gap-3">
        @unless($multiple)
            <div class="relative shrink-0">
                <button
                    type="button"
                    data-file-input-preview-btn
                    class="block w-14 h-14 rounded-md overflow-hidden border border-gray-300 dark:border-gray-600 {{ $currentImage ? '' : 'hidden' }}"
                    title="{{ __('View image') }}"
                >
                    <img
                        data-file-input-preview-img
                        @if($currentImage) src="{{ $currentImage }}" @endif
                        alt="{{ $label }}"
                        class="w-full h-full object-cover"
                    />
                </button>

                <div
                    data-file-input-placeholder
                    class="w-14 h-14 rounded-md border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/40 flex items-center justify-center text-gray-300 dark:text-gray-500 {{ $currentImage ? 'hidden' : '' }}"
                >
                    <i class="ki-filled ki-picture text-lg"></i>
                </div>
            </div>
        @endunless

        <label
            for="{{ $inputId }}"
            data-file-input-label
            class="input ui-file-picker relative flex items-center gap-2 flex-1 cursor-pointer transition-colors
                   {{ $hasError ? 'border-danger' : '' }} {{ $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' }}"
        >
            <span class="btn btn-sm btn-light shrink-0 pointer-events-none">
                <i class="ki-filled ki-folder-up text-sm"></i>
                {{ __('Choose file') }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400 truncate grow" data-file-input-hint>
                {{ $accept ? $accept : __('No file chosen') }}
            </span>
            <span class="text-sm text-gray-700 dark:text-gray-300 truncate grow hidden" data-file-input-filename></span>

            <button
                type="button"
                data-file-input-clear
                class="hidden shrink-0 items-center justify-center w-6 h-6 rounded-full text-gray-400 hover:text-danger hover:bg-danger/10 transition-colors [&:not(.hidden)]:flex"
                title="{{ __('Cancel selection') }}"
            >
                <i class="ki-filled ki-cross text-xs"></i>
            </button>

            <input
                id="{{ $inputId }}"
                aria-invalid="{{ $hasError ? 'true' : 'false' }}"
                @if($errorMsg || $hint || $attributes->get('aria-describedby'))
                    aria-describedby="{{ trim(($attributes->get('aria-describedby') ?? '') . ' ' . (($errorMsg || $hint) ? $inputId . '_feedback' : '')) }}"
                @endif
                data-file-input-field
                data-current-image="{{ $currentImage }}"
                type="file"
                name="{{ $fieldName }}"
                @if($accept) accept="{{ $accept }}" @endif
                @if($multiple) multiple @endif
                @if($disabled) disabled @endif
                @if($isRequired) required @endif
                class="sr-only"
                {{ $attributes->except(['id', 'aria-invalid', 'aria-describedby', 'name', 'accept', 'multiple', 'disabled', 'required', 'class']) }}
            />
        </label>
    </div>

    @if($errorMsg)
        <p id="{{ $inputId }}_feedback" class="text-danger text-xs mt-0.5" role="alert">{{ $errorMsg }}</p>
    @elseif($hint)
        <p id="{{ $inputId }}_feedback" class="form-hint">{{ $hint }}</p>
    @endif

    @unless($multiple)
        <div
            data-file-input-lightbox
            class="hidden fixed inset-0 z-[1000] items-center justify-center bg-black/70 p-6 [&:not(.hidden)]:flex"
        >
            <div class="absolute inset-0" data-file-input-lightbox-close></div>

            <button
                type="button"
                data-file-input-lightbox-close
                class="absolute top-4 end-4 flex items-center justify-center w-9 h-9 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors"
                title="{{ __('Close') }}"
            >
                <i class="ki-filled ki-cross text-lg"></i>
            </button>

            <img
                data-file-input-lightbox-img
                @if($currentImage) src="{{ $currentImage }}" @endif
                alt="{{ $label }}"
                class="relative max-w-full max-h-full rounded-lg shadow-2xl object-contain"
            />
        </div>
    @endunless
</div>
