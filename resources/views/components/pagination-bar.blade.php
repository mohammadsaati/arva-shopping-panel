@props([
    'paginator',
    'perPage',
    'perPageOptions' => [10, 25, 50, 100],
    'model'          => 'perPage',
])

<nav aria-label="{{ __('pagination.page', ['page' => $paginator->currentPage()]) }}" {{ $attributes->merge(['class' => 'ui-pagination flex items-center flex-wrap gap-4 px-5 py-3 border-t border-gray-200 dark:border-gray-700']) }}>

    {{-- Showing X–Y --}}
    <p class="text-sm text-gray-500">
        @if($paginator->firstItem())
            {{ __('pagination.showing', ['from' => $paginator->firstItem(), 'to' => $paginator->lastItem()]) }}
        @endif
    </p>

    {{-- Prev / Page / Next --}}
    <div class="flex items-center gap-1">
        @unless($paginator->onFirstPage())
            <button
                type="button"
                wire:click="previousPage"
                wire:loading.attr="disabled"
                wire:target="previousPage,nextPage"
                class="btn btn-sm btn-light inline-flex items-center gap-1.5"
            >
                <i class="ki-filled ki-arrow-left text-xs"></i>
                {{ __('pagination.previous') }}
            </button>
        @endunless

        <span class="ui-pagination-current text-sm" aria-current="page">
            {{ __('pagination.page', ['page' => $paginator->currentPage()]) }}
        </span>

        @if($paginator->hasMorePages())
            <button
                type="button"
                wire:click="nextPage"
                wire:loading.attr="disabled"
                wire:target="previousPage,nextPage"
                class="btn btn-sm btn-light inline-flex items-center gap-1.5"
            >
                {{ __('pagination.next') }}
                <i class="ki-filled ki-arrow-right text-xs"></i>
            </button>
        @endif
    </div>

    {{-- Per page selector --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 ms-auto">
        <span>{{ __('pagination.show') }}</span>

        <select aria-label="{{ __('pagination.per_page') }}" wire:model.live="{{ $model }}" class="select select-sm w-20">
            @foreach($perPageOptions as $opt)
                <option value="{{ $opt }}" @selected($opt == $perPage)>{{ $opt }}</option>
            @endforeach
        </select>
        <span>{{ __('pagination.per_page') }}</span>
    </div>

</nav>
