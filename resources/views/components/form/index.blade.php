@props([
    'action',
    'method'   => 'POST',
    'enctype'  => null,
    'id'       => null,
    'disabled' => false,
    'class'    => '',
])

@php
    $formId     = $id ?? 'form_' . uniqid();
    $httpMethod = strtoupper($method);
    $realMethod = in_array($httpMethod, ['GET', 'POST']) ? $httpMethod : 'POST';
@endphp

<form
    id="{{ $formId }}"
    method="{{ $realMethod }}"
    action="{{ $action }}"
    @if($enctype) enctype="{{ $enctype }}" @endif
    @if($disabled) class="pointer-events-none opacity-60 {{ $class }}" @else class="{{ $class }}" @endif
>
    @csrf
    @if(! in_array($httpMethod, ['GET', 'POST']))
        @method($httpMethod)
    @endif

    {{ $slot }}
</form>
