<x-panel-layout title="بنرهای وبلاگ" sub-title="بنرهای صفحهٔ اصلی وبلاگ، تصویرهای دسکتاپ و موبایل و زمان نمایش را مدیریت کنید.">
    @push('styles')
        @vite('resources/css/blog-management.css')
    @endpush
    @if (session('success'))<x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>@endif
    @if (session('error'))<x-alert type="danger" class="mb-5">{{ session('error') }}</x-alert>@endif
    <div class="blog-manage-list-intro">
        <span class="blog-manage-intro__icon"><i class="ki-filled ki-picture" aria-hidden="true"></i></span>
        <div><strong>بنرهای صفحهٔ اصلی مجله</strong><p>تصویر دسکتاپ و موبایل، مقصد و زمان نمایش هر بنر را از اینجا مدیریت کنید.</p></div>
    </div>
    <livewire:admin.blog.blog-banner-table />
</x-panel-layout>
