@props([
    'sortable' => false,
    'sort' => null,         // column key to sort by (calls wire:click="sort('<key>')")
    'sortBy' => null,       // current active sort column (from the Livewire component)
    'sortDir' => 'asc',     // current active sort direction
])

<th
    scope="col"
    @if($sortable && $sort) aria-sort="{{ $sortBy === $sort ? ($sortDir === 'asc' ? 'ascending' : 'descending') : 'none' }}" @endif
    {{ $attributes->class([
        'px-5 py-3 text-xs font-semibold uppercase text-gray-500 whitespace-nowrap',
    ]) }}
>
    @if($sortable && $sort)
        <button type="button" class="ui-sort-button" wire:click="sort('{{ $sort }}')">
            {{ $slot }}
            @if($sortBy === $sort)
                <i aria-hidden="true" class="ki-filled {{ $sortDir === 'asc' ? 'ki-arrow-up' : 'ki-arrow-down' }} text-xs text-primary"></i>
            @else
                <i aria-hidden="true" class="ki-filled ki-arrows-circle text-xs text-gray-400"></i>
            @endif
        </button>
    @else
        {{ $slot }}
    @endif
</th>
