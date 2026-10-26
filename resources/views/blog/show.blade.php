@push('styles')
    @vite(['resources/css/blog-home.css', 'resources/css/blog-pages.css'])
@endpush

@push('scripts')
    @vite('resources/js/blog-pages.js')
@endpush

<x-user-layout :title="($post->meta_title ?: $post->title).' | مجله '.site_name()" :description="$post->meta_description ?: $post->excerpt">
    <div class="store-blog blog-page">
        <div class="store-blog__container">
            @include('blog.partials.navigation', ['activeCategory' => $post->category])

            <nav class="blog-page__breadcrumbs" aria-label="مسیر صفحه">
                <a href="{{ route('blog.index') }}">مجله {{ site_name() }}</a>
                <span aria-hidden="true">/</span>
                @if ($post->category->parent)
                    <a href="{{ route('blog.categories.show', $post->category->parent->slug) }}">{{ $post->category->parent->name }}</a>
                    <span aria-hidden="true">/</span>
                @endif
                <a href="{{ route('blog.categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $post->title }}</span>
            </nav>

            <article class="blog-page__post">
                <header class="blog-page__post-head">
                    <a class="blog-page__post-category" href="{{ route('blog.categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                    <h1>{{ $post->title }}</h1>
                    @if ($post->excerpt)<p class="blog-page__post-lead">{{ $post->excerpt }}</p>@endif
                    <div class="blog-page__post-meta">
                        <span><i class="ki-filled ki-calendar" aria-hidden="true"></i> {{ jalali_date($post->published_at) }}</span>
                        @if ($post->author)<span><i class="ki-filled ki-profile-circle" aria-hidden="true"></i> {{ $post->author->name }}</span>@endif
                        <span><i class="ki-filled ki-time" aria-hidden="true"></i> {{ $post->readingMinutes() }} دقیقه مطالعه</span>
                        <span><i class="ki-filled ki-eye" aria-hidden="true"></i> {{ number_format($post->views_count) }} بازدید</span>
                    </div>
                    @include('blog.partials.share', ['shareUrl' => route('blog.posts.show', $post->slug), 'shareText' => $post->title])
                </header>

                @if ($post->featured_image)
                    <figure class="blog-page__post-cover">
                        <img src="{{ public_image_url($post->featured_image) }}" alt="{{ $post->title }}" fetchpriority="high">
                    </figure>
                @endif

                <div class="blog-page__post-layout">
                    <div class="blog-page__post-main">
                        <div class="blog-page__prose">
                            @if ($post->body)
                                {!! \Illuminate\Support\Str::markdown($post->body, ['html_input' => 'strip', 'allow_unsafe_links' => false, 'max_nesting_level' => 100]) !!}
                            @else
                                <p>{{ $post->excerpt ?: 'متن این نوشته هنوز ثبت نشده است.' }}</p>
                            @endif
                        </div>
                        <footer class="blog-page__post-footer">
                            <span>موضوع این نوشته</span>
                            <a class="store-blog__topic" href="{{ route('blog.categories.show', $post->category->slug) }}">{{ $post->category->name }}</a>
                        </footer>
                        @if ($previousPost || $nextPost)
                            <nav class="blog-page__neighbors" aria-label="نوشته‌های همسایه">
                                @if ($previousPost)
                                    <a class="blog-page__neighbor" href="{{ route('blog.posts.show', $previousPost->slug) }}">
                                        <small>نوشته قبلی</small>
                                        <strong>{{ $previousPost->title }}</strong>
                                    </a>
                                @else
                                    <span class="blog-page__neighbor is-empty"></span>
                                @endif
                                @if ($nextPost)
                                    <a class="blog-page__neighbor blog-page__neighbor--next" href="{{ route('blog.posts.show', $nextPost->slug) }}">
                                        <small>نوشته بعدی</small>
                                        <strong>{{ $nextPost->title }}</strong>
                                    </a>
                                @endif
                            </nav>
                        @endif
                    </div>
                    <aside class="blog-page__post-aside" aria-label="پیوندهای مرتبط">
                        <div class="blog-page__aside-box">
                            <span class="blog-page__eyebrow">در همین موضوع</span>
                            <h2>بیشتر بخوانید</h2>
                            @if ($relatedPosts->isNotEmpty())
                                <ul>
                                    @foreach ($relatedPosts as $relatedPost)
                                        <li>
                                            <a href="{{ route('blog.posts.show', $relatedPost->slug) }}">
                                                <strong>{{ $relatedPost->title }}</strong>
                                                <small>{{ jalali_date($relatedPost->published_at) }}</small>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p>نوشته‌های دیگر این موضوع به‌زودی منتشر می‌شوند.</p>
                            @endif
                            <a class="blog-page__aside-all" href="{{ route('blog.categories.show', $post->category->slug) }}">همهٔ مطالب {{ $post->category->name }} <span aria-hidden="true">←</span></a>
                        </div>
                    </aside>
                </div>
            </article>

            @if ($relatedPosts->isNotEmpty())
                <section class="blog-page__related" aria-labelledby="blog-related-title">
                    <div class="blog-page__listing-head">
                        <div>
                            <span class="blog-page__eyebrow">پیشنهاد مطالعه</span>
                            <h2 id="blog-related-title">نوشته‌های مرتبط</h2>
                        </div>
                        <a href="{{ route('blog.categories.show', $post->category->slug) }}">مشاهده همه <span aria-hidden="true">←</span></a>
                    </div>
                    <div class="blog-page__cards">
                        @foreach ($relatedPosts as $relatedPost)
                            @include('blog.partials.post-card', ['post' => $relatedPost])
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="store-blog__closing" aria-labelledby="blog-closing-title">
                <div>
                    <h2 id="blog-closing-title">دنبال ایده‌های بیشتری هستید؟</h2>
                    <p>از نوشته‌های دیگر مجله {{ site_name() }} دیدن کنید.</p>
                </div>
                <a class="store-blog__button" href="{{ route('blog.index') }}">بازگشت به مجله</a>
            </section>
        </div>
    </div>
</x-user-layout>
