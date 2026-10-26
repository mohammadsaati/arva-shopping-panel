<article class="blog-page__card">
    <a class="blog-page__card-image" href="{{ route('blog.posts.show', $post->slug) }}" aria-label="مطالعه {{ $post->title }}">
        <img src="{{ public_image_url($post->featured_image ?: ($post->category->image ?: 'assets/media/blog/editorial-selection.png')) }}" alt="" loading="lazy">
        <span>{{ $post->category->name }}</span>
    </a>
    <div class="blog-page__card-body">
        <div class="blog-page__card-date">
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ jalali_date($post->published_at) }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $post->readingMinutes() }} دقیقه مطالعه</span>
        </div>
        <h3><a href="{{ route('blog.posts.show', $post->slug) }}">{{ $post->title }}</a></h3>
        @if ($post->excerpt)<p>{{ $post->excerpt }}</p>@endif
        <a class="blog-page__card-link" href="{{ route('blog.posts.show', $post->slug) }}">ادامه مطلب <span aria-hidden="true">←</span></a>
    </div>
</article>
