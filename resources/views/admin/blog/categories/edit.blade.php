<x-panel-layout title="ویرایش دسته‌بندی وبلاگ" sub-title="اطلاعات و جایگاه «{{ $category->name }}» را ویرایش کنید.">
    @include('admin.blog.categories.form', ['category' => $category])
</x-panel-layout>
