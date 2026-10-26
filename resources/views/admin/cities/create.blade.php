<x-panel-layout
    title="افزودن شهر"
    sub-title="اطلاعات شهر و استان مربوط به آن را وارد کنید."
>
    <x-card.form
        :action="route('admin.cities.store')"
        method="POST"
        id="create_city_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.cities.index')]"
        :submit="['title' => 'ایجاد شهر', 'type' => 'primary']"
    >
        <x-admin.form-intro title="شهر جدید" description="نام شهر، استان و وضعیت آن را در بخش‌های جدا ثبت کنید." icon="ki-filled ki-geolocation" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="نام نمایشی و شناسهٔ متنی شهر" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام شهر" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="استان" description="جایگاه شهر در فهرست استان‌ها" icon="ki-filled ki-map">
                    <x-form.select
                        name="province_id"
                        label="استان"
                        :options="$provinces"
                        placeholder="استان را انتخاب کنید"
                        required
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت شهر" icon="ki-filled ki-eye">
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="\App\Enums\GlobalStatusEnum::ACTIVE->value"
                        required
                    />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
