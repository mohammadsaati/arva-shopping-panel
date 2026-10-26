@php
    $editing = $banner !== null;
    $desktopImage = $banner?->image ? public_image_url($banner->image) : null;
    $mobileImage = $banner?->mobile_image ? public_image_url($banner->mobile_image) : null;
@endphp

@push('styles')
    @vite('resources/css/blog-management.css')
@endpush
@push('scripts')
    @vite('resources/js/blog-management.js')
@endpush

<x-card.form
    :action="$editing ? route('admin.admin-blog.banners.update', $banner->id) : route('admin.admin-blog.banners.store')"
    :method="$editing ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
    class="blog-manage-form"
    :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.admin-blog.banners.index')]"
    :submit="['title' => $editing ? 'ذخیره تغییرات' : 'ایجاد بنر', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
>
    <div class="blog-manage-intro">
        <span class="blog-manage-intro__icon"><i class="ki-filled ki-picture" aria-hidden="true"></i></span>
        <div>
            <p class="blog-manage-intro__title">بنر صفحهٔ اصلی وبلاگ</p>
            <p class="blog-manage-intro__text">عنوان و مقصد بنر را مشخص کنید، سپس تصویرها و زمان نمایش را تنظیم کنید.</p>
        </div>
    </div>

    <div class="blog-manage-grid">
        <div class="blog-manage-main">
            <section class="blog-manage-section" aria-labelledby="banner-content-heading">
                <div class="blog-manage-section__heading">
                    <span class="blog-manage-section__number">۱</span>
                    <div><h2 id="banner-content-heading">متن بنر</h2><p>پیام بنر را کوتاه و روشن بنویسید.</p></div>
                </div>
                <div class="blog-manage-fields">
                    <x-form.input name="eyebrow" label="متن بالای عنوان" :value="$banner?->eyebrow" placeholder="مثلاً راهنمای خرید" />
                    <x-form.input name="title" label="عنوان بنر" :value="$banner?->title" placeholder="عنوانی که روی بنر دیده می‌شود" required />
                    <x-form.textarea name="description" label="توضیحات کوتاه" :value="$banner?->description" rows="3" placeholder="یک جمله برای توضیح بیشتر" />
                </div>
            </section>

            <section class="blog-manage-section" aria-labelledby="banner-media-heading">
                <div class="blog-manage-section__heading">
                    <span class="blog-manage-section__number">۲</span>
                    <div><h2 id="banner-media-heading">تصویرها</h2><p>نمای دسکتاپ و موبایل را پیش از ذخیره بررسی کنید.</p></div>
                </div>
                <div class="blog-manage-media-grid">
                    <div class="blog-manage-media">
                        <div class="blog-manage-media__header"><strong>دسکتاپ</strong><span>تصویر اصلی · الزامی</span></div>
                        <div class="blog-manage-preview blog-manage-preview--desktop" data-blog-preview="image" data-current-src="{{ $desktopImage }}">
                            <img @if($desktopImage) src="{{ $desktopImage }}" @endif alt="پیش‌نمایش تصویر دسکتاپ" class="{{ $desktopImage ? '' : 'hidden' }}" data-blog-preview-image />
                            <div class="blog-manage-preview__empty {{ $desktopImage ? 'hidden' : '' }}" data-blog-preview-empty><i class="ki-filled ki-picture" aria-hidden="true"></i><span>پیش‌نمایش تصویر دسکتاپ</span></div>
                        </div>
                        <x-form.file name="image" label="تصویر دسکتاپ" accept="image/*" :current-image="$desktopImage" :required="! $editing" hint="حداکثر ۵ مگابایت؛ تصویر افقی پیشنهاد می‌شود." />
                    </div>
                    <div class="blog-manage-media">
                        <div class="blog-manage-media__header"><strong>موبایل</strong><span>اختیاری · جایگزین دسکتاپ</span></div>
                        <div class="blog-manage-preview blog-manage-preview--mobile" data-blog-preview="mobile_image" data-current-src="{{ $mobileImage }}">
                            <img @if($mobileImage) src="{{ $mobileImage }}" @endif alt="پیش‌نمایش تصویر موبایل" class="{{ $mobileImage ? '' : 'hidden' }}" data-blog-preview-image />
                            <div class="blog-manage-preview__empty {{ $mobileImage ? 'hidden' : '' }}" data-blog-preview-empty><i class="ki-filled ki-picture" aria-hidden="true"></i><span>پیش‌نمایش تصویر موبایل</span></div>
                        </div>
                        <x-form.file name="mobile_image" label="تصویر موبایل" accept="image/*" :current-image="$mobileImage" hint="اگر انتخاب نشود، تصویر دسکتاپ نمایش داده می‌شود." />
                        @if ($mobileImage)
                            <x-form.checkbox name="remove_mobile_image" value="1" label="حذف تصویر موبایل فعلی هنگام ذخیره" />
                        @endif
                    </div>
                </div>
            </section>

            <section class="blog-manage-section" aria-labelledby="banner-link-heading">
                <div class="blog-manage-section__heading">
                    <span class="blog-manage-section__number">۳</span>
                    <div><h2 id="banner-link-heading">دکمه و مقصد</h2><p>با کلیک روی بنر، خواننده به این نشانی می‌رود.</p></div>
                </div>
                <div class="blog-manage-fields blog-manage-fields--two">
                    <x-form.input name="target_url" label="پیوند مقصد" :value="$banner?->target_url" dir="ltr" placeholder="/blog/... یا https://..." hint="مسیر داخلی یا نشانی امن https." />
                    <x-form.input name="target_label" label="متن دکمه" :value="$banner?->target_label" placeholder="مثلاً مشاهده مطالب" />
                </div>
            </section>
        </div>

        <aside class="blog-manage-sidebar" aria-label="تنظیمات نمایش بنر">
            <section class="blog-manage-section" aria-labelledby="banner-publish-heading">
                <div class="blog-manage-section__heading blog-manage-section__heading--compact">
                    <span class="blog-manage-section__icon"><i class="ki-filled ki-setting-2" aria-hidden="true"></i></span>
                    <div><h2 id="banner-publish-heading">نمایش بنر</h2><p>وضعیت و جایگاه در اسلایدر</p></div>
                </div>
                <div class="blog-manage-fields">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$banner?->status?->value ?? \App\Enums\GlobalStatusEnum::ACTIVE->value" required />
                    <x-form.input name="sort_order" type="number" min="0" label="ترتیب نمایش" :value="$banner?->sort_order ?? 0" dir="ltr" hint="عدد کوچک‌تر زودتر نمایش داده می‌شود." required />
                </div>
            </section>

            <section class="blog-manage-section" aria-labelledby="banner-schedule-heading">
                <div class="blog-manage-section__heading blog-manage-section__heading--compact">
                    <span class="blog-manage-section__icon"><i class="ki-filled ki-calendar" aria-hidden="true"></i></span>
                    <div><h2 id="banner-schedule-heading">زمان‌بندی</h2><p>در صورت نیاز بازهٔ نمایش را تعیین کنید.</p></div>
                </div>
                <div class="blog-manage-fields">
                    <x-date-picket
                        name="starts_at"
                        label="شروع نمایش"
                        :value="old('starts_at', jalali_date($banner?->starts_at, 'Y/m/d H:i:s') ?? '')"
                        placeholder="انتخاب تاریخ و ساعت"
                    />
                    <p class="-mt-3 text-xs leading-6 text-gray-500">خالی باشد، نمایش از همین حالا شروع می‌شود.</p>
                    <x-date-picket
                        name="ends_at"
                        label="پایان نمایش"
                        :value="old('ends_at', jalali_date($banner?->ends_at, 'Y/m/d H:i:s') ?? '')"
                        placeholder="انتخاب تاریخ و ساعت"
                    />
                    <p class="-mt-3 text-xs leading-6 text-gray-500">خالی باشد، تاریخ پایان ندارد.</p>
                </div>
            </section>
        </aside>
    </div>
</x-card.form>
