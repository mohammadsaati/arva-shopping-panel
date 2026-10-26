<x-panel-layout title="ویرایش بنر وبلاگ" sub-title="محتوا، تصویرها و زمان نمایش «{{ $banner->title }}» را ویرایش کنید.">
    @include('admin.blog.banners.form', ['banner' => $banner])
</x-panel-layout>
