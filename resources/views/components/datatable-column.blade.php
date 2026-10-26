@props([
    'item',                 // the single row item (model/array)
    'field' => null,        // dot-notation key to read from $item; omit when using a slot
    'class' => '',          // extra <td> classes
    'format' => null,       // date | datetime | number | currency | boolean
    'prefix' => null,
    'suffix' => null,
    'empty' => '-',
    'badge' => null,        // ['active' => 'badge-success', '_default' => 'badge-light']
    'html' => false,        // treat the resolved value as raw HTML
    'conditions' => [],      // conditional <td> classes — see DataTable::evalCondition()
])


@php
    $col = \App\View\Components\DataTable::normalizeColumn([
        'key'        => $field ?? '',
        'class'      => $class,
        'format'     => $format,
        'prefix'     => $prefix,
        'suffix'     => $suffix,
        'empty'      => $empty,
        'badge'      => $badge,
        'html'       => $html,
        'conditions' => $conditions,
    ]);
@endphp

<td class="{{ \App\View\Components\DataTable::resolveCellClass($item, $col) }}">
    @if($slot->isNotEmpty())
        {{-- Custom HTML for this cell --}}
        {{ $slot }}
    @else
        {{-- Auto-resolved from $item[$field] --}}
        {!! \App\View\Components\DataTable::resolveCell($item, $col) !!}
    @endif
</td>
