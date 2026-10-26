<x-panel-layout
    :title="'بررسی نظر #' . $review->id"
    sub-title="پیش از تأیید، متن و نکات مثبت و منفی را بررسی کنید."
>
    @if(session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    @php
        $reviewerName = $review->user
            ? trim($review->user->first_name . ' ' . $review->user->last_name)
            : $review->guest_name;
        $positivePoints = $review->points
            ->where('sentiment', \App\Enums\ProductReviewPointSentimentEnum::POSITIVE)
            ->pluck('body')
            ->values()
            ->all();
        $negativePoints = $review->points
            ->where('sentiment', \App\Enums\ProductReviewPointSentimentEnum::NEGATIVE)
            ->pluck('body')
            ->values()
            ->all();
    @endphp

    <section class="card">
        <div class="card-header flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="card-title">{{ $review->title ?: 'بدون عنوان' }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ $review->item->name }} · {{ jalali_date($review->created_at) }}</p>
            </div>
            <x-badge :variant="$review->approval_status->color()" size="sm" dot>{{ $review->approval_status->trans() }}</x-badge>
        </div>
        <div class="card-body grid gap-5">
            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                <span>ثبت‌کننده: <strong class="text-gray-900">{{ $reviewerName ?: 'مهمان' }}</strong></span>
                @if($review->guest_email)
                    <span>ایمیل: {{ $review->guest_email }}</span>
                @endif
                @if($review->user)
                    <span>حساب کاربری #{{ $review->user_id }}</span>
                @endif
                @if($review->verified_purchase_at)
                    <x-badge variant="success" size="sm">خریدار این محصول</x-badge>
                @endif
            </div>
            <div>
                <span class="text-sm text-gray-500">امتیاز</span>
                <p class="mt-1 text-lg font-semibold text-warning">{{ $review->rating }} از ۵ ستاره</p>
            </div>
            <div>
                <h3 class="mb-2 text-sm font-semibold text-gray-700">متن نظر</h3>
                <p class="whitespace-pre-line leading-8 text-gray-700">{{ $review->body }}</p>
            </div>
            @if($review->approved_at)
                <p class="text-xs text-gray-500">تأیید شده در {{ jalali_date($review->approved_at, 'Y/m/d H:i') }}</p>
            @elseif($review->rejected_at)
                <p class="text-xs text-gray-500">رد شده در {{ jalali_date($review->rejected_at, 'Y/m/d H:i') }}</p>
            @endif
        </div>
    </section>

    <x-card.form
        :action="route('admin.product-reviews.update', $review->id)"
        method="PUT"
        title="ویرایش و تعیین وضعیت"
        :cancel="['title' => 'بازگشت به فهرست', 'to' => route('admin.product-reviews.index'), 'type' => 'light']"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
    >
        <x-form.select
            name="approval_status"
            label="وضعیت نمایش نظر"
            :options="$statusOptions"
            :selected="$review->approval_status->value"
            required
            hint="نظر فقط در وضعیت «تأییدشده» در صفحه کالا نمایش داده می‌شود."
        />

        <div class="grid gap-4 xl:grid-cols-2">
            <x-review-points-editor
                name="positive_points"
                title="نکات مثبت"
                hint="نکته مثبت را وارد کنید"
                tone="positive"
                :values="old('positive_points', $positivePoints)"
            />
            <x-review-points-editor
                name="negative_points"
                title="نکات منفی"
                hint="نکته منفی را وارد کنید"
                tone="negative"
                :values="old('negative_points', $negativePoints)"
            />
        </div>
    </x-card.form>
</x-panel-layout>
