<x-panel-layout title="دسته‌بندی‌های وبلاگ" sub-title="دسته‌بندی‌های مجله را جدا از گروه‌بندی کالاهای فروشگاه مدیریت کنید.">
    @push('styles')
        @vite('resources/css/blog-management.css')
    @endpush
    @if (session('success'))<x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>@endif
    @if (session('error'))<x-alert type="danger" class="mb-5">{{ session('error') }}</x-alert>@endif
    <div class="blog-manage-list-intro">
        <span class="blog-manage-intro__icon"><i class="ki-filled ki-category" aria-hidden="true"></i></span>
        <div><strong>ساختار محتوای وبلاگ</strong><p>دسته‌های اصلی و زیرمجموعه‌ها را برای مرتب‌کردن نوشته‌های مجله مدیریت کنید.</p></div>
    </div>
    <livewire:admin.blog.blog-category-table />
</x-panel-layout>
