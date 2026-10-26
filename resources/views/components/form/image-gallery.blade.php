@props([
    'name',
    'existingCount' => 0,
    'maxFiles' => 10,
    'initialSlots' => 3,
    'class' => '',
])

@php
    $maxFiles = max(1, (int) $maxFiles);
    $existingCount = min($maxFiles, max(0, (int) $existingCount));
    $initialCapacity = max(0, $maxFiles - $existingCount);
    $visibleSlots = min(max(1, (int) $initialSlots), $initialCapacity);
@endphp

<section
    data-image-gallery-picker
    data-max-files="{{ $maxFiles }}"
    data-existing-count="{{ $existingCount }}"
    data-initial-slots="{{ min(max(1, (int) $initialSlots), $maxFiles) }}"
    class="rounded-2xl border border-gray-200 bg-gray-50/70 p-4 dark:border-gray-700 dark:bg-gray-800/40 {{ $class }}"
>
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-gray-100">
                <i class="ki-filled ki-picture text-primary" aria-hidden="true"></i>
                افزودن تصاویر گالری
            </h3>
            <p class="mt-1 text-xs leading-6 text-gray-500 dark:text-gray-400">
                @if ($initialCapacity > 0)
                    هر جایگاه یک عکس می‌گیرد؛ برای تصاویر بیشتر، دکمه افزودن جایگاه را بزنید. سقف گالری {{ $maxFiles }} تصویر است.
                @else
                    گالری به سقف {{ $maxFiles }} تصویر رسیده است. برای افزودن عکس، ابتدا از تصاویر فعلی موردی را برای حذف انتخاب کنید.
                @endif
            </p>
        </div>
        <span data-image-gallery-counter class="rounded-full bg-white px-3 py-1 text-xs font-medium text-gray-600 shadow-sm dark:bg-gray-700 dark:text-gray-200" aria-live="polite"></span>
    </div>

    <div data-image-gallery-slots class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @for ($slot = 0; $slot < $maxFiles; $slot++)
            <div
                data-image-gallery-slot
                data-slot-index="{{ $slot }}"
                @if ($slot >= $visibleSlots) hidden @endif
                class="{{ $slot >= $visibleSlots ? 'hidden ' : '' }}min-w-0 rounded-xl border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900/50"
            >
                <div class="mb-2 flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">تصویر {{ $slot + 1 }}</span>
                    <span class="text-[11px] text-gray-400">اختیاری</span>
                </div>
                <x-form.file
                    :name="$name . '[' . $slot . ']'"
                    accept="image/*"
                    hint="یک تصویر انتخاب کنید"
                />
            </div>
        @endfor
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
        <p data-image-gallery-limit-message class="hidden text-xs text-danger" role="alert"></p>
        <button
            type="button"
            data-image-gallery-add
            class="btn btn-sm btn-light inline-flex items-center gap-2"
        >
            <i class="ki-filled ki-plus" aria-hidden="true"></i>
            افزودن جایگاه تصویر
        </button>
    </div>
</section>
