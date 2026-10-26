@props([
    'label'    => null,
    'for'      => null,
    'hint'     => null,
    'error'    => null,
    'required' => false,
    'class'    => '',
])

<div class="ui-field flex flex-col gap-1 {{ $class }}">
    @if($label)
        <label
            @if($for) for="{{ $for }}" @endif
            class="form-label"
        >
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($error)
        <p class="text-danger text-xs mt-0.5">{{ $error }}</p>
    @elseif($hint)
        <p class="form-hint">{{ $hint }}</p>
    @endif
</div>
