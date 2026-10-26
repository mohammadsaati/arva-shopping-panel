@props([
    'action',
    'method' => 'POST',
    'enctype' => null,
    'id' => null,
    'title' => null,
    'cancel' => null,
    'submit' => null,
    'class' => '',
])

@php
    $httpMethod = strtoupper($method instanceof BackedEnum ? $method->value : (string) $method);
    $realMethod = in_array($httpMethod, ['GET', 'POST']) ? $httpMethod : 'POST';
    $formId = $id ?? 'form_' . uniqid();
@endphp
<form
    class="{{ $class }}"
    id="{{ $formId }}"
    method="{{ $realMethod }}"
    action="{{ $action }}"
    @if($enctype) enctype="{{ $enctype }}" @endif
    {{ $attributes->except('class') }}
>
    @csrf
    @if(! in_array($httpMethod, ['GET', 'POST']))
        @method($httpMethod)
    @endif

    <div class="card">
        @if($title)
            <div class="card-header">
                <h3 class="card-title">{{ $title }}</h3>
            </div>
        @endif
        <div class="card-body grid gap-5">
            {{ $slot }}
        </div>
        @if($cancel || $submit)
            <div class="ui-form-actions">
            @if($cancel)
                <x-button
                    :variant="data_get($cancel, 'type', 'light')"
                    :href="data_get($cancel, 'to')"
                    :icon="data_get($cancel, 'icon')"
                    :disabled="data_get($cancel, 'disabled', false)"
                >
                    {{ data_get($cancel, 'title', 'انصراف') }}
                </x-button>
            @endif
            @if($submit)
                <button
                    type="submit"
                    class="btn btn-{{ data_get($submit, 'type', 'primary') }} inline-flex items-center gap-2"
                    @if(data_get($submit, 'disabled', false)) disabled @endif
                >
                    @if(data_get($submit, 'icon'))
                        <i class="{{ data_get($submit, 'icon') }}" aria-hidden="true"></i>
                    @endif
                    {{ data_get($submit, 'title', 'ذخیره') }}
                    <span data-btn-spinner class="hidden inline-block h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent"></span>
                </button>
            @endif
            </div>
        @endif
    </div>
</form>
