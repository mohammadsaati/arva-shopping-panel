<section class="product-reviews" id="product-reviews" aria-labelledby="product-reviews-title">
    <header class="product-reviews__header">
        <div>
            <span class="product-reviews__eyebrow">تجربه خریداران</span>
            <h2 id="product-reviews-title">دیدگاه کاربران</h2>
            <p>نظرهای تأییدشده درباره این کالا</p>
        </div>
        @auth
            <button type="button" class="btn-site-primary product-reviews__write" data-open-review-dialog>
                <span aria-hidden="true">✍️</span>
                ثبت نظر و امتیاز
            </button>
        @else
            <a class="btn-site-primary product-reviews__write" href="{{ route('login') }}">
                <span aria-hidden="true">✍️</span>
                ورود برای ثبت نظر
            </a>
        @endauth
    </header>

    <div class="product-reviews__summary" aria-label="خلاصه امتیاز کاربران">
        <div class="product-reviews__average">
            <strong>{{ number_format($summary['average'], 1) }}</strong>
            <span aria-hidden="true">⭐</span>
            <small>از ۵</small>
            <p>{{ number_format($summary['count']) }} نظر تأییدشده</p>
        </div>
        <div class="product-reviews__distribution">
            @for($rating = 5; $rating >= 1; $rating--)
                @php
                    $ratingCount = $summary['distribution'][$rating];
                    $ratingPercent = $summary['count'] > 0 ? (int) round($ratingCount / $summary['count'] * 100) : 0;
                @endphp
                <div class="product-reviews__distribution-row">
                    <span>{{ $rating }} ستاره</span>
                    <span class="product-reviews__bar" aria-hidden="true"><span style="width: {{ $ratingPercent }}%"></span></span>
                    <span>{{ number_format($ratingCount) }}</span>
                </div>
            @endfor
        </div>
    </div>

    @if (session('review_submitted'))
        <p class="product-reviews__notice" role="status">{{ session('review_submitted') }}</p>
    @endif

    <div class="product-reviews__list" aria-live="polite">
        @forelse($reviews as $review)
            @php
                $reviewerName = $review->user
                    ? trim($review->user->first_name . ' ' . $review->user->last_name)
                    : $review->guest_name;
            @endphp
            <article class="product-review-card">
                <header class="product-review-card__header">
                    <div class="product-review-card__author">
                        <span class="product-review-card__avatar" aria-hidden="true">{{ mb_substr($reviewerName ?: 'م', 0, 1) }}</span>
                        <div>
                            <strong>{{ $reviewerName ?: 'خریدار مهمان' }}</strong>
                            <time datetime="{{ $review->created_at->toDateString() }}">{{ jalali_date($review->created_at) }}</time>
                        </div>
                    </div>
                    <div class="product-review-card__rating" aria-label="امتیاز {{ $review->rating }} از ۵">
                        <span aria-hidden="true">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                        <b>{{ $review->rating }}/۵</b>
                    </div>
                </header>

                @if($review->verified_purchase_at)
                    <span class="product-review-card__verified"><span aria-hidden="true">✓</span> خریدار این محصول</span>
                @endif

                @if($review->title)
                    <h3 class="product-review-card__title">{{ $review->title }}</h3>
                @endif
                <p class="product-review-card__body">{{ $review->body }}</p>

                @php
                    $positivePoints = $review->points->where('sentiment', \App\Enums\ProductReviewPointSentimentEnum::POSITIVE);
                    $negativePoints = $review->points->where('sentiment', \App\Enums\ProductReviewPointSentimentEnum::NEGATIVE);
                @endphp
                @if($positivePoints->isNotEmpty() || $negativePoints->isNotEmpty())
                    <div class="product-review-card__points" role="list" aria-label="نکات مثبت و منفی">
                        @if($positivePoints->isNotEmpty())
                            @foreach($positivePoints as $point)
                                <span class="product-review-point-badge product-review-point-badge--positive" role="listitem">
                                    <span class="product-review-point-badge__icon" aria-hidden="true">✓</span>
                                    <span class="sr-only">نکته مثبت: </span>
                                    <span>{{ $point->body }}</span>
                                </span>
                            @endforeach
                        @endif
                        @if($negativePoints->isNotEmpty())
                            @foreach($negativePoints as $point)
                                <span class="product-review-point-badge product-review-point-badge--negative" role="listitem">
                                    <span class="product-review-point-badge__icon" aria-hidden="true">−</span>
                                    <span class="sr-only">نکته منفی: </span>
                                    <span>{{ $point->body }}</span>
                                </span>
                            @endforeach
                        @endif
                    </div>
                @endif
            </article>
        @empty
            <div class="product-reviews__empty">
                <span aria-hidden="true">💬</span>
                <h3>هنوز نظری برای این کالا ثبت نشده</h3>
                <p>تجربه‌تان را بنویسید تا به خریداران دیگر کمک کنید.</p>
            </div>
        @endforelse
    </div>

    @if($reviews->hasPages())
        <nav class="product-reviews__pagination" aria-label="صفحه‌بندی دیدگاه‌ها">{{ $reviews->links() }}</nav>
    @endif

    @auth
    <dialog class="product-review-dialog" data-review-dialog data-auto-open="{{ $errors->any() ? 'true' : 'false' }}" aria-labelledby="product-review-dialog-title">
        <div class="product-review-dialog__heading">
            <div>
                <span aria-hidden="true">✨</span>
                <h2 id="product-review-dialog-title">تجربه‌ات از این کالا چطور بود؟</h2>
                <p>امتیاز و نظرت را با خریداران دیگر به اشتراک بگذار.</p>
            </div>
            <button type="button" class="product-review-dialog__close" data-close-review-dialog aria-label="بستن پنجره">×</button>
        </div>

        <x-card.form
            class="product-review-form"
            :action="route('product-reviews.store', ['itemId' => $itemId])"
            :submit="['title' => 'ثبت نظر برای بررسی', 'type' => 'primary']"
        >
            <div class="product-review-form__section product-review-form__section--rating">
                <x-form.rating
                    name="rating"
                    label="چه امتیازی به این کالا می‌دهی؟"
                    :options="[
                        1 => ['emoji' => '😣', 'label' => 'خیلی بد'],
                        2 => ['emoji' => '🙁', 'label' => 'بد'],
                        3 => ['emoji' => '😐', 'label' => 'معمولی'],
                        4 => ['emoji' => '🙂', 'label' => 'خوب'],
                        5 => ['emoji' => '🤩', 'label' => 'عالی'],
                    ]"
                    required
                />
            </div>

            <section class="product-review-form__section" aria-labelledby="product-review-content-title">
                <div class="product-review-form__section-heading">
                    <h3 id="product-review-content-title">دیدگاه شما</h3>
                    <p>تجربه‌ات را کوتاه و دقیق بنویس تا انتخاب برای دیگران راحت‌تر شود.</p>
                </div>
                <div class="product-review-form__content-fields">
                    <x-form.input name="title" label="عنوان نظر" placeholder="مثلاً کیفیت خوب و استفاده راحت" hint="اختیاری؛ یک جمله کوتاه کافی است." maxlength="150" />
                    <x-form.textarea name="body" label="متن نظر" placeholder="چه چیزی را دوست داشتی؟ چه چیزی می‌توانست بهتر باشد؟" hint="تجربه واقعی استفاده‌ات را بنویس." rows="5" required maxlength="5000" />
                </div>
            </section>

            <section class="product-review-form__section" aria-labelledby="product-review-points-title">
                <div class="product-review-form__section-heading">
                    <h3 id="product-review-points-title">نکات مهم <span>اختیاری</span></h3>
                    <p>ویژگی‌های خوب یا قابل‌بهبود را جداگانه اضافه کن.</p>
                </div>
                <div class="product-review-form__points">
                    <x-review-points-editor
                        name="positive_points"
                        title="نکات مثبت"
                        hint="یک نکته مثبت بنویس"
                        tone="positive"
                        :values="old('positive_points', [])"
                    />
                    <x-review-points-editor
                        name="negative_points"
                        title="نکات منفی"
                        hint="یک نکته قابل‌بهبود بنویس"
                        tone="negative"
                        :values="old('negative_points', [])"
                    />
                </div>
            </section>

            <p class="product-review-form__moderation"><span aria-hidden="true">ⓘ</span> نظر شما پیش از نمایش بررسی می‌شود.</p>
        </x-card.form>
    </dialog>
    @endauth
</section>
