@extends('layouts.panel')

@section('content')
    <main class="grow">
        <!-- Toolbar -->
        <div class="panel-toolbar">
            <!-- Container -->
            <div class="container-fixed flex items-center justify-between flex-wrap gap-3">
                <div class="flex flex-col flex-wrap gap-1">
                    <h1 class="font-medium text-lg text-gray-900">
                        {{ $title }}
                    </h1>
                    @if($subTitle)
                        <p class="text-sm font-normal">{{ $subTitle }}</p>
                    @endif
                </div>
                @if (isset($actions))
                    <div class="flex items-center gap-2">
                        {{ $actions }}
                    </div>
                @endif
            </div>
            <!-- End of Container -->
        </div>
        <!-- End of Toolbar -->
        <!-- Container -->
        <div class="container-fixed">
            <div class="grid gap-5 lg:gap-7.5">
                {{ $slot }}
            </div>
        </div>
        <!-- End of Container -->
    </main>
@endsection
