@php
    $editing = $category !== null;
    $categoryImage = $category?->image ? public_image_url($category->image) : null;
@endphp

@push('styles')
    @vite('resources/css/blog-management.css')
@endpush
@push('scripts')
    @vite('resources/js/blog-management.js')
@endpush

<x-card.form
    :action="$editing ? route('admin.admin-blog.categories.update', $category->id) : route('admin.admin-blog.categories.store')"
    :method="$editing ? 'PUT' : 'POST'"
    enctype="multipart/form-data"
    class="blog-manage-form"
    data-blog-category-editing="{{ $editing ? '1' : '0' }}"
    :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.admin-blog.categories.index')]"
    :submit="['title' => $editing ? 'ذخیره تغییرات' : 'ایجاد دسته‌بندی', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
>
    <div class="blog-manage-intro">
        <span class="blog-manage-intro__icon"><i class="ki-filled ki-category" aria-hidden="true"></i></span>
        <div>
            <p class="blog-manage-intro__title">دسته‌بندی مطالب وبلاگ</p>
            <p class="blog-manage-intro__text">نام، توضیح و تصویر دسته را آماده کنید؛ جایگاه آن در ستون کناری مشخص می‌شود.</p>
        </div>
    </div>

    <div class="blog-manage-grid">
        <div class="blog-manage-main">
            <section class="blog-manage-section" aria-labelledby="category-identity-heading">
                <div class="blog-manage-section__heading">
                    <span class="blog-manage-section__number">۱</span>
                    <div><h2 id="category-identity-heading">نام و معرفی</h2><p>یک نام مشخص انتخاب کنید تا خواننده موضوع مطالب را سریع پیدا کند.</p></div>
                </div>
                <div class="blog-manage-fields">
                    <x-form.input name="name" label="نام دسته‌بندی" :value="$category?->name" placeholder="مثلاً راهنمای خرید" data-blog-category-name required />
                    <x-form.input name="slug" label="شناسهٔ متنی" :value="$category?->slug" dir="ltr" hint="از نام پیشنهاد می‌شود و هر زمان قابل ویرایش است." data-blog-category-slug required />
                    <x-form.textarea name="description" label="توضیحات دسته‌بندی" :value="$category?->description" rows="5" placeholder="در چند جمله بگویید چه مطالبی در این دسته قرار می‌گیرند." />
                </div>
            </section>

            <section class="blog-manage-section" aria-labelledby="category-image-heading">
                <div class="blog-manage-section__heading">
                    <span class="blog-manage-section__number">۲</span>
                    <div><h2 id="category-image-heading">تصویر دسته‌بندی</h2><p>تصویری انتخاب کنید که موضوع دسته را در یک نگاه نشان دهد.</p></div>
                </div>
                <div class="blog-manage-media">
                    <div class="blog-manage-preview blog-manage-preview--category" data-blog-preview="image" data-current-src="{{ $categoryImage }}">
                        <img @if($categoryImage) src="{{ $categoryImage }}" @endif alt="پیش‌نمایش تصویر دسته‌بندی" class="{{ $categoryImage ? '' : 'hidden' }}" data-blog-preview-image />
                        <div class="blog-manage-preview__empty {{ $categoryImage ? 'hidden' : '' }}" data-blog-preview-empty><i class="ki-filled ki-picture" aria-hidden="true"></i><span>پیش‌نمایش تصویر دسته‌بندی</span></div>
                    </div>
                    <x-form.file name="image" label="انتخاب تصویر" accept="image/*" :current-image="$categoryImage" hint="اختیاری؛ حداکثر ۵ مگابایت." />
                    @if ($categoryImage)
                        <x-form.checkbox name="remove_image" value="1" label="حذف تصویر فعلی هنگام ذخیره" />
                    @endif
                </div>
            </section>
        </div>

        <aside class="blog-manage-sidebar" aria-label="تنظیمات دسته‌بندی">
            <section class="blog-manage-section" aria-labelledby="category-publish-heading">
                <div class="blog-manage-section__heading blog-manage-section__heading--compact">
                    <span class="blog-manage-section__icon"><i class="ki-filled ki-setting-2" aria-hidden="true"></i></span>
                    <div><h2 id="category-publish-heading">نمایش</h2><p>وضعیت و اولویت دسته</p></div>
                </div>
                <div class="blog-manage-fields">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$category?->status?->value ?? \App\Enums\GlobalStatusEnum::ACTIVE->value" required />
                    <x-form.input name="priority" type="number" min="0" label="اولویت نمایش" :value="$category?->priority ?? 0" dir="ltr" hint="عدد کوچک‌تر زودتر نمایش داده می‌شود." required />
                </div>
            </section>

            <section class="blog-manage-section" aria-labelledby="category-parent-heading">
                <div class="blog-manage-section__heading blog-manage-section__heading--compact">
                    <span class="blog-manage-section__icon"><i class="ki-filled ki-abstract-26" aria-hidden="true"></i></span>
                    <div><h2 id="category-parent-heading">جایگاه</h2><p>دستهٔ والد در ساختار وبلاگ</p></div>
                </div>
                <x-form.select2 name="parent_id" label="دسته‌بندی والد" :options="$parents" :selected="$category?->parent_id" placeholder="دستهٔ اصلی (بدون والد)" :clearable="true" hint="دسته‌های وبلاگ از فروشگاه جدا هستند." />
            </section>
        </aside>
    </div>
</x-card.form>
