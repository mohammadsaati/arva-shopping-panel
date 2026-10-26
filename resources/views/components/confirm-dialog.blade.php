@props([
    'id',
    'title' => 'تأیید عملیات',
    'message' => 'آیا از انجام این کار مطمئن هستید؟',
    'confirmText' => 'تأیید',
    'cancelText' => 'انصراف',
    'errorText' => 'انجام این کار ممکن نشد. دوباره تلاش کنید.',
    'variant' => 'primary',
])

<dialog
    id="{{ $id }}"
    aria-labelledby="{{ $id }}_title"
    aria-describedby="{{ $id }}_message"
    aria-modal="true"
    data-confirm-dialog
    wire:ignore
    class="app-confirm-dialog m-auto w-[calc(100%-2rem)] max-w-sm rounded-2xl border border-gray-200 bg-white p-0 text-gray-900 shadow-2xl backdrop:bg-gray-950/60 backdrop:backdrop-blur-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
    {{ $attributes->except(['id', 'class', 'aria-labelledby', 'aria-describedby', 'aria-modal']) }}
>
    <div class="p-6">
        <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-full bg-warning-light text-warning" aria-hidden="true">
            <i class="ki-filled ki-information-2 text-xl"></i>
        </div>
        <h3 id="{{ $id }}_title" data-confirm-title class="text-base font-semibold">{{ $title }}</h3>
        <p id="{{ $id }}_message" data-confirm-message class="mt-2 text-sm leading-7 text-gray-600 dark:text-gray-300">{{ $message }}</p>
        <p data-confirm-error data-default-error="{{ $errorText }}" class="mt-3 hidden text-sm text-danger" role="alert"></p>

        <div class="mt-6 flex flex-wrap justify-end gap-2">
            <x-button type="button" variant="light" size="sm" data-confirm-cancel>{{ $cancelText }}</x-button>
            <x-button type="button" :variant="$variant" size="sm" data-confirm-accept>{{ $confirmText }}</x-button>
        </div>
    </div>
</dialog>
