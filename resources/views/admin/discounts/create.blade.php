<x-panel-layout title="افزودن کد تخفیف" sub-title="کد، مبلغ، بازه اعتبار و شرایط استفاده را تنظیم کنید.">
    <x-card.form
        :action="route('admin.discounts.store')"
        method="POST"
        id="create_discount_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.discounts.index')]"
        :submit="['title' => 'ایجاد کد تخفیف', 'type' => 'primary']"
    >
        @include('admin.discounts.form-fields')
    </x-card.form>
</x-panel-layout>
