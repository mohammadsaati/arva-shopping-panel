@props([
    'activeCount' => 0,
    'title' => 'فیلتر و جستجو',
    'embedded' => false,
    'resettable' => true,
])

@php
    $activeCount = (int) $activeCount;
    $panelClass = 'ui-filter-panel';
@endphp

@if ($embedded)
    <div {{ $attributes->class([$panelClass]) }}>
@else
    <div {{ $attributes->class(['card mb-5 admin-list-filters']) }}>
        <div @class(['card-body', $panelClass])>
@endif

        <header class="ui-filter-panel__header">
            <div class="ui-filter-panel__title">
                <span class="ui-filter-panel__icon" aria-hidden="true"><i class="ki-filled ki-filter"></i></span>
                <span>{{ $title }}</span>
                @if ($activeCount > 0)
                    <span class="badge badge-sm badge-light ui-filter-panel__count">{{ $activeCount }} فیلتر فعال</span>
                @endif
            </div>

            <div class="ui-filter-panel__toolbar">
                @isset($actions)
                    <div class="ui-filter-panel__actions">{{ $actions }}</div>
                @endisset

                @if ($resettable && $activeCount > 0)
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="btn btn-sm btn-light ui-filter-panel__reset inline-flex items-center gap-1.5"
                    >
                        <i class="ki-filled ki-arrows-circle text-gray-500" aria-hidden="true"></i>
                        پاک کردن فیلترها
                    </button>
                @endif
            </div>
        </header>

        <div class="ui-filter-panel__grid">
            {{ $slot }}
        </div>

        @if (isset($footer))
            <div class="ui-filter-panel__footer">
                {{ $footer }}
            </div>
        @endif

@if ($embedded)
    </div>
@else
        </div>
    </div>
@endif
