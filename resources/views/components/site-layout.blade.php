@props([
    'title' => null,
    'categories' => null,
])

<x-user-layout :title="$title" :categories="$categories">
    {{ $slot }}
</x-user-layout>
