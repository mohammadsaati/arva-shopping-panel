<x-panel-layout title="ویرایش کمیسیون" sub-title="عنوان و درصد این کمیسیون را ویرایش کنید.">
    <x-card.form
        action="{{ route('admin.commissions.update', $commission->id) }}"
        method="PUT"
        id="edit_commission_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.commissions.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="ویرایش کمیسیون" description="تغییر درصد روی سفارش‌های جدید اثر دارد؛ درصد سفارش‌های ثبت‌شده ثابت می‌ماند." icon="ki-filled ki-percentage" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات کمیسیون" description="عنوانی روشن و درصدی بین صفر تا صد وارد کنید." icon="ki-filled ki-percentage" :two-columns="true">
                    <x-form.input name="title" label="عنوان" :value="$commission->title" required />
                    <x-form.input name="percentage" label="درصد" type="number" min="0" max="100" step="0.01" :value="number_format($commission->rate_bps / 100, 2, '.', '')" suffix="٪" required />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
