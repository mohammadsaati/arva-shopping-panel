@push('styles')
    @vite('resources/css/blog-home.css')
@endpush

@push('scripts')
    @vite('resources/js/blog-home.js')
@endpush

<x-user-layout :title="'مجله '.site_name().' | راهنمای خرید و انتخاب آگاهانه'">
    <livewire:storefront.blog-home />
</x-user-layout>
