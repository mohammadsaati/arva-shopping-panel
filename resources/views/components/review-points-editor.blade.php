@props([
    'name',
    'title',
    'hint',
    'tone',
    'values' => [],
])

<section
    class="review-points-editor review-points-editor--{{ $tone }}"
    data-review-points-editor
    data-name="{{ $name }}"
    data-next-index="{{ count($values) > 0 ? max(array_keys($values)) + 1 : 0 }}"
    data-max="10"
>
    <header class="review-points-editor__header">
        <div>
            <h4>{{ $title }}</h4>
            <p>{{ $hint }}</p>
        </div>
        <span class="review-points-editor__count" data-review-points-count>{{ count($values) }}/۱۰</span>
    </header>
    <div class="review-points-editor__list" data-review-points-list>
        @foreach($values as $index => $value)
            <div class="review-points-editor__row" data-review-point-row>
                <x-form.input
                    :name="$name . '[' . $index . ']'"
                    :value="$value"
                    :label="$title"
                    :placeholder="$hint"
                    maxlength="255"
                />
                <button type="button" class="review-points-editor__remove" data-review-point-remove aria-label="حذف این نکته">حذف</button>
            </div>
        @endforeach
    </div>
    <template data-review-points-template>
        <div class="review-points-editor__row" data-review-point-row>
            <x-form.input
                :name="$name . '[__INDEX__]'"
                :label="$title"
                :placeholder="$hint"
                maxlength="255"
            />
            <button type="button" class="review-points-editor__remove" data-review-point-remove aria-label="حذف این نکته">حذف</button>
        </div>
    </template>
    <button type="button" class="review-points-editor__add" data-review-point-add>
        <span aria-hidden="true">+</span>
        افزودن نکته
    </button>
</section>
