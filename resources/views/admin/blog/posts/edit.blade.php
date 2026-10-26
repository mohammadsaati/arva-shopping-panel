<x-panel-layout title="ویرایش نوشته" sub-title="محتوا و تنظیمات انتشار «{{ $post->title }}» را ویرایش کنید.">
    @include('admin.blog.posts.form', ['post' => $post])
</x-panel-layout>
