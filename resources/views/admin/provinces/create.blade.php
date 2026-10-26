<x-panel-layout
    title="افزودن استان"
    sub-title="نام استان و وضعیت نمایش آن را ثبت کنید."
>
    <x-card.form
        :action="route('admin.provinces.store')"
        method="POST"
        id="create_province_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.provinces.index')]"
        :submit="['title' => 'ایجاد استان', 'type' => 'primary']"
    >
        <x-admin.form-intro title="استان" description="نام استان را ثبت کنید و وضعیت نمایش آن را مشخص کنید." icon="ki-filled ki-map" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات استان" description="نامی که در فهرست‌ها دیده می‌شود." icon="ki-filled ki-text">
                    <x-form.input name="name" label="نام استان" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات تکمیلی">
                <x-admin.form-section title="نمایش" description="وضعیت استان" icon="ki-filled ki-eye">
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
