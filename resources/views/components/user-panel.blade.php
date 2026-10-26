@props([
    'title' => null,
    'subTitle' => null,
    'categories' => null,
])

<x-user-layout :title="$title" :categories="$categories">
    <section class="user-page">
        @if ($title || $subTitle || isset($actions))
            <header class="user-page__heading">
                <div>
                    @if ($title)
                        <h1 class="user-page__title">{{ $title }}</h1>
                    @endif
                    @if ($subTitle)
                        <p class="user-page__subtitle">{{ $subTitle }}</p>
                    @endif
                </div>

                @isset($actions)
                    <div class="flex items-center gap-2">
                        {{ $actions }}
                    </div>
                @endisset
            </header>
        @endif

        {{ $slot }}
    </section>
</x-user-layout>
