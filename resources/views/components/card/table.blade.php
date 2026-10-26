@props([
    'title'      => null,
    'subtitle'   => null,
    'headers'    => [],
    'striped'    => false,
    'hoverable'  => true,
    'empty'      => null,
    'class'      => '',
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

    <div class="card-body p-0">
        @if(isset($filters))
            <div class="px-5 py-3 border-b border-gray-200">
                {{ $filters }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table {{ $striped ? 'table-striped' : '' }} {{ $hoverable ? 'table-hover' : '' }} w-full text-sm">
                @if(count($headers))
                    <thead>
                        <tr class="text-xs uppercase text-gray-500 border-b border-gray-200">
                            @foreach($headers as $header)
                                <th class="px-5 py-3 font-semibold whitespace-nowrap">
                                    {{ is_array($header) ? $header['label'] : $header }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                @endif

                <tbody class="divide-y divide-gray-100">
                    {{ $slot }}
                </tbody>
            </table>
        </div>

        @if(isset($empty) && $slot->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-gray-400">
                <i class="ki-filled ki-data text-5xl mb-3"></i>
                <p class="text-sm">{{ $empty }}</p>
            </div>
        @endif
    </div>

    @if(isset($pagination))
        <div class="card-footer flex items-center justify-between">
            {{ $pagination }}
        </div>
    @endif
</div>
