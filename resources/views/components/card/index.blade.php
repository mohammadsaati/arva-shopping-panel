@props([
    'title'    => null,
    'subtitle' => null,
    'flush'    => false,
    'class'    => '',
])

<div class="card {{ $class }}" {{ $attributes->except('class') }}>
    @if($title || $subtitle || isset($actions))
        <div class="card-header">
            <div class="flex flex-col gap-0.5">
                @if($title)
                    <h3 class="card-title">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $flush ? '' : 'card-body' }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
