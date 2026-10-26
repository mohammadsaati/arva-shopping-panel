<x-panel-layout title="نوشته‌های وبلاگ" sub-title="نوشته‌ها را مدیریت کنید و مطالب شاخص صفحهٔ مجله را انتخاب کنید.">
    @if (session('success'))<x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>@endif
    @if (session('error'))<x-alert type="danger" class="mb-5">{{ session('error') }}</x-alert>@endif
    <livewire:admin.blog.blog-post-table />
</x-panel-layout>
