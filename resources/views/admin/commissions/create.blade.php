<x-panel-layout title="افزودن کمیسیون" sub-title="یک عنوان و درصد برای انتخاب فروشندگان تعریف کنید.">
    <x-card.form
        action="{{ route('admin.commissions.store') }}"
        method="POST"
        id="create_commission_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.commissions.index')]"
        :submit="['title' => 'ایجاد کمیسیون', 'type' => 'primary']"
    >
        <x-admin.form-intro title="کمیسیون جدید" description="پس از ثبت، می‌توانید آن را برای هر فروشنده انتخاب کنید؛ انتخاب کمیسیون اختیاری است." icon="ki-filled ki-percentage" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات کمیسیون" description="عنوانی روشن و درصدی بین صفر تا صد وارد کنید." icon="ki-filled ki-percentage" :two-columns="true">
                    <x-form.input name="title" label="عنوان" required />
                    <x-form.input name="percentage" label="درصد" type="number" min="0" max="100" step="0.01" suffix="٪" required />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
