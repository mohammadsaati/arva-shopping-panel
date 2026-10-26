@php
    $editing = $post !== null;
    $formAction = $editing
        ? route('admin.admin-blog.posts.update', $post->id)
        : route('admin.admin-blog.posts.store');
@endphp

@push('styles')
    @vite('resources/css/blog-editor.css')
@endpush
@push('scripts')
    @vite('resources/js/blog-editor.js')
@endpush

<x-card.form
    :action="$formAction"
    :method="$editing ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
    id="blog-post-form"
    class="blog-post-form"
    data-blog-post-editing="{{ $editing ? '1' : '0' }}"
    :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => $editing ? route('admin.admin-blog.posts.show', $post->id) : route('admin.admin-blog.posts.index')]"
    :submit="['title' => $editing ? 'ذخیره تغییرات' : 'ذخیره نوشته', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
>
    <div class="blog-post-intro">
        <span class="blog-post-intro__mark"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
        <div>
            <p class="blog-post-intro__eyebrow">محیط نوشتن وبلاگ</p>
            <p class="blog-post-intro__text">اول عنوان و متن را آماده کنید. دسته‌بندی، تصویر و وضعیت انتشار در تنظیمات کنار صفحه قرار دارند.</p>
        </div>
    </div>

    <div class="blog-post-grid">
        <div class="blog-post-main">
            <section class="blog-post-section" aria-labelledby="blog-post-content-title">
                <div class="blog-post-section__heading">
                    <span class="blog-post-section__number">۱</span>
                    <div><h2 id="blog-post-content-title">عنوان و متن نوشته</h2><p>عنوان روشن و محتوای خوانا، پایهٔ هر مطلب خوب است.</p></div>
                </div>
                <div class="blog-post-fields">
                    <x-form.input name="title" label="عنوان نوشته" :value="$post?->title" placeholder="عنوانی بنویسید که موضوع مطلب را دقیق بگوید" class="blog-post-title-field" data-blog-title required />
                    <x-form.input name="slug" label="شناسهٔ متنی" :value="$post?->slug" dir="ltr" hint="از عنوان پیشنهاد می‌شود؛ می‌توانید آن را تغییر دهید." data-blog-slug required />
                    <x-form.rich-editor name="body" label="متن نوشته" :value="$post?->body" hint="برای انتشار، وارد کردن متن الزامی است. از نوار ابزار برای تیترها، فهرست و پیوند استفاده کنید." />
                </div>
            </section>

            <section class="blog-post-section" aria-labelledby="blog-post-excerpt-title">
                <div class="blog-post-section__heading">
                    <span class="blog-post-section__number">۲</span>
                    <div><h2 id="blog-post-excerpt-title">خلاصهٔ نوشته</h2><p>یک معرفی کوتاه برای کارت مطلب و صفحهٔ وبلاگ.</p></div>
                </div>
                <x-form.textarea name="excerpt" label="خلاصه" :value="$post?->excerpt" rows="4" placeholder="در چند جمله بگویید خواننده در این مطلب چه چیزی یاد می‌گیرد." />
            </section>
        </div>

        <aside class="blog-post-sidebar" aria-label="تنظیمات نوشته">
            <section class="blog-post-section" aria-labelledby="blog-post-publish-title">
                <div class="blog-post-section__heading blog-post-section__heading--compact">
                    <span class="blog-post-section__icon"><i class="ki-filled ki-calendar" aria-hidden="true"></i></span>
                    <div><h2 id="blog-post-publish-title">انتشار</h2><p>وضعیت و زمان نمایش مطلب</p></div>
                </div>
                <div class="blog-post-fields">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$post?->status?->value ?? \App\Enums\BlogPostStatusEnum::DRAFT->value" required />
                    <x-date-picket
                        name="published_at"
                        label="زمان انتشار"
                        :value="old('published_at', jalali_date($post?->published_at, 'Y/m/d H:i:s') ?? '')"
                        placeholder="انتخاب تاریخ و ساعت"
                    />
                    <p class="-mt-3 text-xs leading-6 text-gray-500">اگر خالی بماند، زمان فعلی هنگام انتشار ثبت می‌شود.</p>
                </div>
            </section>

            <section class="blog-post-section" aria-labelledby="blog-post-category-title">
                <div class="blog-post-section__heading blog-post-section__heading--compact">
                    <span class="blog-post-section__icon"><i class="ki-filled ki-category" aria-hidden="true"></i></span>
                    <div><h2 id="blog-post-category-title">دسته‌بندی</h2><p>جای این مطلب در وبلاگ</p></div>
                </div>
                <x-form.select2 name="blog_category_id" label="دسته‌بندی وبلاگ" :options="$categories" :selected="$post?->blog_category_id" placeholder="انتخاب دسته‌بندی" :clearable="false" required />
            </section>

            <section class="blog-post-section" aria-labelledby="blog-post-image-title">
                <div class="blog-post-section__heading blog-post-section__heading--compact">
                    <span class="blog-post-section__icon"><i class="ki-filled ki-picture" aria-hidden="true"></i></span>
                    <div><h2 id="blog-post-image-title">تصویر شاخص</h2><p>تصویر کارت و کاور نوشته</p></div>
                </div>
                <div class="blog-post-fields">
                    <x-form.file name="featured_image" label="انتخاب تصویر" accept="image/*" :current-image="$post?->featured_image ? public_image_url($post->featured_image) : null" hint="حداکثر ۵ مگابایت؛ نسبت افقی برای کاور مناسب‌تر است." />
                    @if ($post?->featured_image)
                        <x-form.checkbox name="remove_featured_image" value="1" label="حذف تصویر فعلی هنگام ذخیره" />
                    @endif
                </div>
            </section>

            <section class="blog-post-section" aria-labelledby="blog-post-featured-title">
                <div class="blog-post-section__heading blog-post-section__heading--compact">
                    <span class="blog-post-section__icon"><i class="ki-filled ki-star" aria-hidden="true"></i></span>
                    <div><h2 id="blog-post-featured-title">مطالب شاخص</h2><p>نمایش در اسلایدر وبلاگ</p></div>
                </div>
                <div class="blog-post-fields">
                    <x-form.checkbox name="is_featured" value="1" label="نمایش در اسلایدر مطالب شاخص" :checked="$post?->is_featured ?? false" />
                    <x-form.input name="featured_order" type="number" min="0" label="ترتیب در اسلایدر" :value="$post?->featured_order ?? 0" dir="ltr" />
                </div>
            </section>

            <section class="blog-post-section" aria-labelledby="blog-post-seo-title">
                <div class="blog-post-section__heading blog-post-section__heading--compact">
                    <span class="blog-post-section__icon"><i class="ki-filled ki-search-list" aria-hidden="true"></i></span>
                    <div><h2 id="blog-post-seo-title">نمایش در جستجو</h2><p>عنوان و توضیح برای موتورهای جستجو</p></div>
                </div>
                <div class="blog-post-fields">
                    <x-form.input name="meta_title" label="عنوان SEO" :value="$post?->meta_title" placeholder="در صورت خالی بودن، عنوان نوشته استفاده می‌شود" />
                    <x-form.textarea name="meta_description" label="توضیحات SEO" :value="$post?->meta_description" rows="3" placeholder="خلاصه‌ای کوتاه از موضوع مطلب" />
                </div>
            </section>
        </aside>
    </div>
</x-card.form>
