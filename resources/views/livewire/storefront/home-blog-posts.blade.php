<div class="store-home-blog-wrapper">
    @if ($posts->isNotEmpty())
        @php
            $featured = $posts->first();
            $morePosts = $posts->skip(1)->values();
        @endphp
        <section class="store-section store-home-blog" aria-labelledby="store-home-blog-title">
            <div class="store-section__heading store-home-blog__heading">
                <div>
                    <p class="store-section__eyebrow">مجله {{ site_name() }}</p>
                    <h2 id="store-home-blog-title" class="store-section__title">برای انتخاب بهتر، بیشتر بدانید</h2>
                </div>
                <a class="store-home-blog__all" href="{{ route('blog.index') }}">
                    همه نوشته‌ها
                    <span aria-hidden="true">←</span>
                </a>
            </div>

            <div @class(['store-home-blog__island', 'is-solo' => $morePosts->isEmpty()])>
                <article class="store-home-blog__feature">
                    <a class="store-home-blog__feature-media" href="{{ route('blog.posts.show', $featured->slug) }}" aria-hidden="true" tabindex="-1">
                        <img
                            src="{{ public_image_url($featured->featured_image ?: ($featured->category->image ?: 'assets/media/blog/editorial-selection.png')) }}"
                            alt=""
                            loading="lazy"
                        >
                        <span class="store-home-blog__fresh">تازه‌ترین</span>
                    </a>
                    <div class="store-home-blog__feature-copy">
                        <span class="store-home-blog__tag">{{ $featured->category->name }}</span>
                        <h3>
                            <a href="{{ route('blog.posts.show', $featured->slug) }}">{{ $featured->title }}</a>
                        </h3>
                        @if ($featured->excerpt)
                            <p>{{ $featured->excerpt }}</p>
                        @endif
                        <div class="store-home-blog__meta">
                            <time datetime="{{ $featured->published_at->toDateString() }}">{{ jalali_date($featured->published_at) }}</time>
                            <span aria-hidden="true">·</span>
                            <span>{{ $featured->readingMinutes() }} دقیقه مطالعه</span>
                        </div>
                        <a class="store-home-blog__cta" href="{{ route('blog.posts.show', $featured->slug) }}">
                            مطالعه نوشته
                            <span aria-hidden="true">←</span>
                        </a>
                    </div>
                </article>

                @if ($morePosts->isNotEmpty())
                    <ul class="store-home-blog__list">
                        @foreach ($morePosts as $post)
                            <li wire:key="home-blog-post-{{ $post->id }}">
                                <a class="store-home-blog__item" href="{{ route('blog.posts.show', $post->slug) }}">
                                    <span class="store-home-blog__item-media">
                                        <img
                                            src="{{ public_image_url($post->featured_image ?: ($post->category->image ?: 'assets/media/blog/editorial-selection.png')) }}"
                                            alt=""
                                            loading="lazy"
                                        >
                                        <span class="store-home-blog__item-index">{{ sprintf('%02d', $loop->iteration) }}</span>
                                    </span>
                                    <span class="store-home-blog__item-copy">
                                        <small>{{ $post->category->name }} · {{ jalali_date($post->published_at) }}</small>
                                        <strong>{{ $post->title }}</strong>
                                        <em>{{ $post->readingMinutes() }} دقیقه مطالعه</em>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>
    @endif
</div>
