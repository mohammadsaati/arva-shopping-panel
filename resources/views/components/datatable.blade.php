@props([
    'tableId' => null,
    'title' => null,
    'description' => null,
])

@php
    $resolvedTableId = $tableId ?? 'datatable_' . uniqid();
@endphp

<div id="{{ $resolvedTableId }}" {{ $attributes->class(['card ui-datatable']) }}>

    {{-- Header --}}
    @if($title || $description || isset($actions))
        <div class="card-header flex-wrap gap-3">
            <div class="flex flex-col gap-0.5">
                @if($title)
                    <h3 class="card-title">{{ $title }}</h3>
                @endif
                @if($description)
                    <p class="text-sm text-gray-500">{{ $description }}</p>
                @endif
            </div>
            @isset($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    {{-- Table --}}
    <div class="card-table">
        <div class="overflow-x-auto">
            <table class="table table-border align-middle text-sm [&_tbody_tr]:transition-colors">
                <thead>
                    <tr>
                        {{ $head ?? '' }}
                    </tr>
                </thead>

                {{-- The caller loops $items and emits one <tr> of <x-datatable-column> cells per row. --}}
                <tbody>
                    {{ $slot }}
                </tbody>
            </table>
        </div>
    </div>
</div>
