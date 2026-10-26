@props([
    'label',
    'emptyLabel' => 'همه موارد',
])

<x-admin.filter-field :label="$label">
    <select
        {{ $attributes->class(['select select-sm w-full border-gray-200']) }}
    >
        <option value="">{{ $emptyLabel }}</option>
        {{ $slot }}
    </select>
</x-admin.filter-field>
