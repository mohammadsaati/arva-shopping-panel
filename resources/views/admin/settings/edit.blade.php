<x-panel-layout title="تنظیمات سایت" sub-title="هویت فروشگاه، راه‌های ارتباطی، محتوای فوتر و سئوی پیش‌فرض را مدیریت کنید.">
    @if (session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif

    <div class="flex flex-wrap gap-2">
        <a class="btn btn-light" href="{{ route('admin.telegram.index') }}">اتصال تلگرام ادمین</a>
        <a class="btn btn-light" href="{{ route('admin.footer.index', 'features') }}">مزیت‌های فوتر</a>
        <a class="btn btn-light" href="{{ route('admin.footer.index', 'groups') }}">ستون‌های لینک</a>
        <a class="btn btn-light" href="{{ route('admin.footer.index', 'links') }}">لینک‌های فوتر</a>
        <a class="btn btn-light" href="{{ route('admin.footer.index', 'social') }}">شبکه‌های اجتماعی</a>
        <a class="btn btn-light" href="{{ route('admin.footer.index', 'apps') }}">لینک‌های اپلیکیشن</a>
        <a class="btn btn-light" href="{{ route('admin.newsletter-subscribers.index') }}">اعضای خبرنامه</a>
    </div>

    <x-card.form action="{{ route('admin.settings.update') }}" method="PUT" enctype="multipart/form-data" class="admin-editor-form" :submit="['title' => 'ذخیره تنظیمات', 'type' => 'primary', 'icon' => 'ki-filled ki-check']">
        <x-admin.form-intro title="هویت فروشگاه" description="نام و تصاویر برند در هدر، فوتر و عنوان صفحه استفاده می‌شوند." icon="ki-filled ki-setting-2" />

        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و راه‌های ارتباطی" description="اطلاعاتی که خریدار در سراسر سایت می‌بیند." icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="site_name" label="نام سایت" :value="$settings?->site_name ?? config('app.name')" required />
                    <x-form.input name="site_tagline" label="شعار کوتاه" :value="$settings?->site_tagline" />
                    <x-form.input name="support_phone" label="تلفن پشتیبانی" :value="$settings?->support_phone" dir="ltr" />
                    <x-form.input name="support_phone_secondary" label="تلفن دوم" :value="$settings?->support_phone_secondary" dir="ltr" />
                    <x-form.input name="support_hours" label="ساعت پاسخ‌گویی" :value="$settings?->support_hours" placeholder="مثلاً هر روز، ۹ تا ۲۱" />
                    <x-form.input name="contact_email" type="email" label="ایمیل تماس" :value="$settings?->contact_email" dir="ltr" />
                    <x-form.textarea name="contact_address" label="آدرس تماس" :value="$settings?->contact_address" rows="2" />
                </x-admin.form-section>

                <x-admin.form-section title="تصاویر برند" description="تصویر جدید جایگزین تصویر قبلی می‌شود؛ برای حذف، گزینهٔ همان تصویر را انتخاب کنید." icon="ki-filled ki-picture" :two-columns="true">
                    @foreach (['logo' => ['لوگوی هدر', 'logo_path'], 'footer_logo' => ['لوگوی فوتر', 'footer_logo_path'], 'favicon' => ['آیکون مرورگر', 'favicon_path'], 'app_logo' => ['لوگوی اپلیکیشن', 'app_logo_path']] as $field => [$label, $column])
                        <div class="grid gap-2">
                            <x-form.file :name="$field" :label="$label" accept="image/*" :current-image="$settings?->getAttribute($column) ? public_image_url($settings->getAttribute($column)) : null" />
                            @if ($settings?->getAttribute($column))<x-form.checkbox :name="'remove_'.$field" label="حذف {{ $label }}" />@endif
                        </div>
                    @endforeach
                </x-admin.form-section>

                <x-admin.form-section title="محتوای فوتر" description="متن برند، خبرنامه و نوار اپلیکیشن؛ آیتم‌های تکرارشونده از دکمه‌های بالای صفحه مدیریت می‌شوند." icon="ki-filled ki-menu" :two-columns="true">
                    <x-form.textarea name="footer_description" label="معرفی کوتاه فروشگاه" :value="$settings?->footer_description" rows="3" />
                    <x-form.input name="copyright_text" label="متن کپی‌رایت" :value="$settings?->copyright_text" placeholder="اگر خالی باشد، سال و نام سایت نمایش داده می‌شود" />
                    <x-form.input name="social_title" label="عنوان شبکه‌های اجتماعی" :value="$settings?->social_title" placeholder="همراه ما باشید" />
                    <x-form.input name="newsletter_title" label="عنوان خبرنامه" :value="$settings?->newsletter_title" placeholder="از تازه‌ترین تخفیف‌ها باخبر شوید" />
                    <x-form.textarea name="newsletter_description" label="توضیح خبرنامه" :value="$settings?->newsletter_description" rows="2" />
                    <x-form.input name="app_download_title" label="عنوان دانلود اپلیکیشن" :value="$settings?->app_download_title" />
                    <x-form.textarea name="app_download_description" label="توضیح اپلیکیشن" :value="$settings?->app_download_description" rows="2" />
                </x-admin.form-section>
            </div>

            <aside class="admin-form-sidebar">
                <x-admin.form-section title="سئوی پیش‌فرض" description="در صفحاتی که عنوان یا توضیح اختصاصی ندارند استفاده می‌شود." icon="ki-filled ki-graph-up" compact>
                    <x-form.input name="meta_title" label="عنوان متا" :value="$settings?->meta_title" />
                    <x-form.textarea name="meta_description" label="توضیحات متا" :value="$settings?->meta_description" rows="3" />
                    <x-form.file name="og_image" label="تصویر اشتراک‌گذاری" accept="image/*" :current-image="$settings?->og_image_path ? public_image_url($settings->og_image_path) : null" hint="تصویر افقی با نسبت حدود ۱٫۹ به ۱ پیشنهاد می‌شود." />
                    @if ($settings?->og_image_path)<x-form.checkbox name="remove_og_image" label="حذف تصویر اشتراک‌گذاری" />@endif
                    <x-form.hidden name="robots_index" value="0" />
                    <x-form.checkbox name="robots_index" label="اجازهٔ ایندکس صفحات" :checked="$settings?->robots_index ?? true" />
                    <x-form.hidden name="robots_follow" value="0" />
                    <x-form.checkbox name="robots_follow" label="اجازهٔ دنبال‌کردن لینک‌ها" :checked="$settings?->robots_follow ?? true" />
                    <x-form.input name="google_site_verification" label="کد تأیید گوگل" :value="$settings?->google_site_verification" dir="ltr" hint="فقط مقدار content را وارد کنید، نه تگ کامل HTML." />
                    <x-form.input name="bing_site_verification" label="کد تأیید بینگ" :value="$settings?->bing_site_verification" dir="ltr" hint="فقط مقدار content را وارد کنید." />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
