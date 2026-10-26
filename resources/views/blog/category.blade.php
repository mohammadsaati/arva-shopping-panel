@push('styles')
    @vite(['resources/css/blog-home.css', 'resources/css/blog-pages.css'])
@endpush

<x-user-layout :title="$pageTitle.' | مجله '.site_name()">
    <livewire:storefront.blog-category-posts :slug="$categorySlug" />
</x-user-layout>
