@props([
    'name',
    'value' => null,
    'label' => 'متن نوشته',
    'hint' => null,
    'class' => '',
])

@php
    $inputId = 'rich_editor_' . str_replace(['.', '[', ']'], '_', $name);
    $errorMessage = $errors->first($name);
    $oldValue = old($name, $value);
@endphp

<div class="ui-field blog-rich-field {{ $class }}" data-blog-editor>
    <label for="{{ $inputId }}" class="form-label" data-blog-editor-label>{{ $label }}</label>

    <div class="blog-rich-shell {{ $errorMessage ? 'blog-rich-shell--invalid' : '' }}">
        <div class="blog-rich-toolbar hidden" data-blog-editor-toolbar role="toolbar" aria-label="ابزارهای ویرایش متن">
            <div class="blog-rich-toolbar__group" role="group" aria-label="تیترها">
                <button type="button" data-editor-action="paragraph" aria-label="پاراگراف" title="پاراگراف">متن</button>
                <button type="button" data-editor-action="heading2" aria-label="تیتر سطح دو" title="تیتر سطح دو" dir="ltr">H2</button>
                <button type="button" data-editor-action="heading3" aria-label="تیتر سطح سه" title="تیتر سطح سه" dir="ltr">H3</button>
            </div>
            <div class="blog-rich-toolbar__group" role="group" aria-label="قالب‌بندی متن">
                <button type="button" data-editor-action="bold" aria-label="پررنگ" title="پررنگ" dir="ltr"><strong>B</strong></button>
                <button type="button" data-editor-action="italic" aria-label="مورب" title="مورب" dir="ltr"><em>I</em></button>
                <button type="button" data-editor-action="link" aria-label="افزودن پیوند" title="افزودن پیوند">پیوند</button>
            </div>
            <div class="blog-rich-toolbar__group" role="group" aria-label="ساختار متن">
                <button type="button" data-editor-action="bulletList" aria-label="فهرست نشانه‌دار" title="فهرست نشانه‌دار">• ≡</button>
                <button type="button" data-editor-action="orderedList" aria-label="فهرست شماره‌دار" title="فهرست شماره‌دار" dir="ltr">1. ≡</button>
                <button type="button" data-editor-action="blockquote" aria-label="نقل‌قول" title="نقل‌قول">❞</button>
                <button type="button" data-editor-action="horizontalRule" aria-label="جداکننده" title="جداکننده">―</button>
            </div>
            <div class="blog-rich-toolbar__group" role="group" aria-label="بازگردانی تغییرات">
                <button type="button" data-editor-action="undo" aria-label="بازگشت" title="بازگشت"><i class="ki-filled ki-arrow-right" aria-hidden="true"></i></button>
                <button type="button" data-editor-action="redo" aria-label="تکرار" title="تکرار"><i class="ki-filled ki-arrow-left" aria-hidden="true"></i></button>
            </div>
        </div>

        <div class="blog-rich-link-panel hidden" data-blog-editor-link-panel>
            <label for="{{ $inputId }}_link" class="form-label">نشانی پیوند</label>
            <div class="blog-rich-link-panel__controls">
                <input id="{{ $inputId }}_link" type="text" inputmode="url" dir="ltr" data-blog-editor-link-input placeholder="https://example.com یا /blog/..." aria-label="نشانی پیوند" />
                <button type="button" data-blog-editor-link-apply class="btn btn-sm btn-primary">ثبت</button>
                <button type="button" data-blog-editor-link-remove class="btn btn-sm btn-light">حذف پیوند</button>
                <button type="button" data-blog-editor-link-close class="blog-rich-link-panel__close" aria-label="بستن"><i class="ki-filled ki-cross" aria-hidden="true"></i></button>
            </div>
            <p class="blog-rich-link-panel__error hidden" data-blog-editor-link-error role="alert">نشانی باید با https://، / یا # شروع شود.</p>
        </div>

        <div class="blog-rich-canvas hidden" data-blog-editor-canvas></div>
        <textarea
            id="{{ $inputId }}"
            name="{{ $name }}"
            rows="16"
            class="textarea blog-rich-fallback {{ $errorMessage ? 'border-danger' : '' }}"
            aria-invalid="{{ $errorMessage ? 'true' : 'false' }}"
            @if ($hint || $errorMessage) aria-describedby="{{ $inputId }}_feedback" @endif
            data-blog-editor-input
        >{{ $oldValue }}</textarea>

        <div class="blog-rich-footer">
            <span>متن را با تیتر، فهرست و پیوند خواناتر کنید.</span>
            <span class="hidden" data-blog-editor-count aria-live="polite"></span>
        </div>
    </div>

    @if ($errorMessage)
        <p id="{{ $inputId }}_feedback" class="text-danger text-xs" role="alert">{{ $errorMessage }}</p>
    @elseif ($hint)
        <p id="{{ $inputId }}_feedback" class="form-hint">{{ $hint }}</p>
    @endif
</div>
