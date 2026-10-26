<x-panel-layout title="ویرایش کد تخفیف" sub-title="قواعد کد تخفیف و تعداد کالاهای مجاز را به‌روزرسانی کنید.">
    <x-card.form
        :action="route('admin.discounts.update', $discount->id)"
        method="PUT"
        id="edit_discount_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.discounts.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        @include('admin.discounts.form-fields', ['discount' => $discount])
    </x-card.form>
</x-panel-layout>
