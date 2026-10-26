<x-panel-layout
    title="افزودن رنگ"
    sub-title="نام نمایشی و مقدار رنگ را برای انتخاب در محصولات وارد کنید."
>
    <x-card.form
        action="{{ route('admin.colors.store') }}"
        method="POST"
        id="create_color_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.colors.index')]"
        :submit="['title' => 'ایجاد رنگ', 'type' => 'primary']"
    >
        <x-admin.form-intro title="رنگ محصولات" description="نام و نمونهٔ رنگ را کنار هم مشخص کنید." icon="ki-filled ki-colors-square" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات رنگ" description="نام نمایشی و مقدار واقعی رنگ" icon="ki-filled ki-color-swatch" :two-columns="true">
                    <x-form.input name="name" label="نام رنگ" placeholder="برای نمونه: قرمز" required />
                    <x-form.input name="value" label="مقدار رنگ" type="color" value="#000000" hint="رنگ را از انتخابگر مشخص کنید." required />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
