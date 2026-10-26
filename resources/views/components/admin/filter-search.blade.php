@props([
    'label' => 'جستجو',
    'placeholder' => 'جستجو...',
    'debounce' => '300ms',
    'wide' => true,
])

<x-admin.filter-field :label="$label" :wide="$wide">
    <div class="input flex items-center gap-2 border-gray-200 transition focus-within:border-primary">
        <i class="ki-filled ki-magnifier shrink-0 text-gray-400" aria-hidden="true"></i>
        <input
            type="search"
            wire:model.live.debounce.{{ $debounce }}="search"
            aria-label="{{ $label }}"
            placeholder="{{ $placeholder }}"
            class="grow bg-transparent text-sm outline-none placeholder:text-gray-400"
            {{ $attributes->except(['label', 'placeholder', 'debounce', 'wide']) }}
        />
    </div>
</x-admin.filter-field>
