<x-panel-layout
    title="ویرایش سایز"
    sub-title="مقدار سایز را ویرایش کنید."
>
    <x-card.form
        action="{{ route('admin.sizes.update', $size->id) }}"
        method="PUT"
        id="edit_size_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.sizes.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="سایز محصولات" description="مقداری وارد کنید که در انتخاب تنوع محصول روشن و قابل فهم باشد." icon="ki-filled ki-text" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="مقدار سایز" description="مثلاً XL یا ۴۲" icon="ki-filled ki-text">
                    <x-form.input name="value" label="مقدار سایز" :value="$size->value" required />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
