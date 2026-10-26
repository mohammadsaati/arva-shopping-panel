<x-panel-layout
    title="ویرایش شهر"
    sub-title="نام، شناسه متنی، استان یا وضعیت شهر را به‌روزرسانی کنید."
>
    <x-card.form
        :action="route('admin.cities.update', $city->id)"
        method="PUT"
        id="edit_city_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.cities.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="شهر جدید" description="نام شهر، استان و وضعیت آن را در بخش‌های جدا ثبت کنید." icon="ki-filled ki-geolocation" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="نام نمایشی و شناسهٔ متنی شهر" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام شهر" :value="$city->name" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" :value="$city->slug" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="استان" description="جایگاه شهر در فهرست استان‌ها" icon="ki-filled ki-map">
                    <x-form.select
                        name="province_id"
                        label="استان"
                        :options="$provinces"
                        :selected="$city->province_id"
                        required
                    />
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت شهر" icon="ki-filled ki-eye">
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="$city->status->value"
                        required
                    />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
