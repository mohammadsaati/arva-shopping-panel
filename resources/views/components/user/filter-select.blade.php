@props(['id', 'label', 'placeholder' => 'همه موارد'])

<div class="store-filter-select ui-select2" data-select2 data-searchable="true" data-clearable="true" data-placeholder="{{ $placeholder }}">
    <label for="{{ $id }}" data-select2-label data-select2-for="{{ $id }}-trigger" class="store-filter-select__label">{{ $label }}</label>
    <select id="{{ $id }}" class="ui-select2__native" data-select2-native {{ $attributes->except(['id', 'class']) }}>
        <option value="" data-select2-placeholder>{{ $placeholder }}</option>
        {{ $slot }}
    </select>
    <div class="ui-select2__control">
        <div id="{{ $id }}-trigger" class="ui-select2__trigger" data-select2-trigger role="combobox" tabindex="0" aria-haspopup="listbox" aria-expanded="false" aria-controls="{{ $id }}-options" aria-label="{{ $label }}">
            <span class="ui-select2__value" data-select2-value></span>
        </div>
        <div class="ui-select2__actions">
            <button type="button" class="ui-select2__clear" data-select2-clear aria-label="پاک کردن {{ $label }}" hidden>×</button>
            <span class="ui-select2__chevron" aria-hidden="true"></span>
        </div>
    </div>
    <div class="ui-select2__panel" data-select2-panel hidden>
        <div class="ui-select2__search-wrap">
            <input type="search" class="ui-select2__search" data-select2-search placeholder="جستجوی {{ $label }}..." aria-label="جستجوی {{ $label }}" autocomplete="off" tabindex="-1">
        </div>
        <div id="{{ $id }}-options" class="ui-select2__options" data-select2-options role="listbox"></div>
        <p class="ui-select2__empty" data-select2-empty hidden>گزینه‌ای پیدا نشد.</p>
    </div>
</div>
