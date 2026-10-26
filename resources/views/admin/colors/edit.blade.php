<x-panel-layout
    title="ویرایش رنگ"
    sub-title="نام و مقدار رنگ را ویرایش کنید."
>
    <x-card.form
        action="{{ route('admin.colors.update', $color->id) }}"
        method="PUT"
        id="edit_color_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.colors.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="رنگ محصولات" description="نام و نمونهٔ رنگ را کنار هم مشخص کنید." icon="ki-filled ki-colors-square" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="مشخصات رنگ" description="نام نمایشی و مقدار واقعی رنگ" icon="ki-filled ki-color-swatch" :two-columns="true">
                    <x-form.input name="name" label="نام رنگ" :value="$color->name" required />
                    <x-form.input name="value" label="مقدار رنگ" type="color" :value="$color->value" hint="رنگ را از انتخابگر مشخص کنید." required />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
