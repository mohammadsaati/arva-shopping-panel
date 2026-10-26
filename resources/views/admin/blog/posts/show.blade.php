@push('styles')
    @vite('resources/css/blog-editor.css')
@endpush

<x-panel-layout :title="$post->title" sub-title="جزئیات نوشتهٔ وبلاگ.">
    <x-card title="اطلاعات نوشته" class="mb-5">
        <div class="grid gap-5 sm:grid-cols-2">
            <div><span class="text-sm text-gray-500">دسته‌بندی وبلاگ</span><p class="mt-1 font-medium">{{ $post->category->name }}</p></div>
            <div><span class="text-sm text-gray-500">وضعیت</span><p class="mt-1"><x-badge :variant="$post->status->color()" size="sm" dot>{{ $post->status->trans() }}</x-badge></p></div>
            <div><span class="text-sm text-gray-500">نویسنده</span><p class="mt-1">{{ $post->author?->name ?? '—' }}</p></div>
            <div><span class="text-sm text-gray-500">تاریخ انتشار</span><p class="mt-1">{{ jalali_date($post->published_at, 'Y/m/d H:i') ?? '—' }}</p></div>
            <div><span class="text-sm text-gray-500">شناسهٔ متنی</span><p class="mt-1" dir="ltr">{{ $post->slug }}</p></div>
            <div><span class="text-sm text-gray-500">مطالب شاخص</span><p class="mt-1">{{ $post->is_featured ? 'بله · ترتیب ' . ($post->featured_order + 1) : 'خیر' }}</p></div>
            @if ($post->featured_image)
                <div class="sm:col-span-2"><span class="text-sm text-gray-500">تصویر شاخص</span><img src="{{ public_image_url($post->featured_image) }}" alt="{{ $post->title }}" class="mt-2 max-h-80 rounded-xl object-cover" /></div>
            @endif
            <div class="sm:col-span-2"><span class="text-sm text-gray-500">خلاصه</span><p class="mt-1 leading-7">{{ $post->excerpt ?: '—' }}</p></div>
            <div class="sm:col-span-2">
                <span class="text-sm text-gray-500">متن نوشته</span>
                @if ($post->body)
                    <div class="blog-post-prose mt-2 rounded-xl border border-gray-200 bg-gray-50 p-5 dark:bg-gray-800">
                        {!! \Illuminate\Support\Str::markdown($post->body, ['html_input' => 'strip', 'allow_unsafe_links' => false, 'max_nesting_level' => 100]) !!}
                    </div>
                @else
                    <p class="mt-2 text-gray-400">—</p>
                @endif
            </div>
            <div><span class="text-sm text-gray-500">عنوان SEO</span><p class="mt-1">{{ $post->meta_title ?: '—' }}</p></div>
            <div><span class="text-sm text-gray-500">توضیحات SEO</span><p class="mt-1">{{ $post->meta_description ?: '—' }}</p></div>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <form method="POST" action="{{ route('admin.admin-blog.posts.destroy', $post->id) }}" data-confirm-open="blog-delete-confirm" data-confirm-title="حذف نوشته" data-confirm-message="نوشتهٔ «{{ $post->title }}» و تصویر شاخص آن حذف شود؟" data-confirm-text="حذف نوشته">
                @csrf
                @method('DELETE')
                <x-button type="submit" variant="danger" icon="ki-filled ki-trash">حذف نوشته</x-button>
            </form>
            <x-button href="{{ route('admin.admin-blog.posts.index') }}" variant="light">بازگشت</x-button>
            <x-button href="{{ route('admin.admin-blog.posts.edit', $post->id) }}" icon="ki-filled ki-notepad-edit">ویرایش نوشته</x-button>
        </div>
    </x-card>
    <x-confirm-dialog id="blog-delete-confirm" variant="danger" confirm-text="حذف" />
</x-panel-layout>
