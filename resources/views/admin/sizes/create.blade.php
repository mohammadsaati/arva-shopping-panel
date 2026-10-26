<x-panel-layout
    title="افزودن سایز"
    sub-title="مقدار سایز را وارد کنید تا در فرم محصولات قابل انتخاب باشد."
>
    <x-card.form
        action="{{ route('admin.sizes.store') }}"
        method="POST"
        id="create_size_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.sizes.index')]"
        :submit="['title' => 'ایجاد سایز', 'type' => 'primary']"
    >
        <x-admin.form-intro title="سایز محصولات" description="مقداری وارد کنید که در انتخاب تنوع محصول روشن و قابل فهم باشد." icon="ki-filled ki-text" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="مقدار سایز" description="مثلاً XL یا ۴۲" icon="ki-filled ki-text">
                    <x-form.input name="value" label="مقدار سایز" placeholder="برای نمونه: XL یا ۴۲" required />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
