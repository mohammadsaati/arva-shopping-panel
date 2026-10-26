<x-panel-layout
    :title="$specification ? 'ویرایش مشخصه' : 'افزودن مشخصه'"
    :sub-title="'مشخصات دسته‌بندی ' . $category->name"
>
    <x-card.form
        :action="$specification
            ? route('admin.categories.specifications.fields.update', [$category->id, $specification->id])
            : route('admin.categories.specifications.fields.store', $category->id)"
        :method="$specification ? 'PUT' : 'POST'"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.categories.specifications.index', $category->id)]"
        :submit="['title' => 'ذخیره مشخصه', 'type' => 'primary']"
    >
        <x-admin.form-intro title="مشخصهٔ کالا" description="عنوان و نوع مقدار را تعریف کنید؛ جایگاه و نحوهٔ نمایش در کارت‌های کناری تنظیم می‌شوند." icon="ki-filled ki-setting-2" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و نوع مشخصه" description="کلید ثابت و عنوانی که خریدار می‌بیند." icon="ki-filled ki-text" :two-columns="true">
            <x-form.input name="key" label="کد ثابت" :value="$specification?->key" dir="ltr" hint="مثلاً supported_languages؛ پس از ثبت مقدار، کد را ثابت نگه دارید." required />
            <x-form.input name="name" label="عنوان نمایشی" :value="$specification?->name" required />
            <x-form.select name="type" label="نوع مقدار" :options="$typeOptions" :selected="$specification?->type?->value" required />
                </x-admin.form-section>
                <x-admin.form-section title="گزینه‌های مقدار" description="برای نوع انتخاب تکی یا چندگانه، هر گزینه را در یک خط وارد کنید." icon="ki-filled ki-text">
            <x-form.textarea
                name="options"
                label="گزینه‌ها"
                :value="$specification?->options->map(fn ($option) => $option->value . '|' . $option->label)->implode(PHP_EOL)"
                rows="6"
                hint="فقط برای انتخاب تکی یا چندگانه. هر خط: کد|عنوان؛ مثلاً android|اندروید"
            />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات مشخصه">
                <x-admin.form-section title="جایگاه" description="گروه، واحد و ترتیب نمایش" icon="ki-filled ki-category">
                    <x-form.select name="group_id" label="گروه" :options="$groups->pluck('name', 'id')->all()" :selected="$specification?->specification_group_id" required />
                    <x-form.input name="unit" label="واحد (اختیاری)" :value="$specification?->unit" placeholder="مثلاً گیگابایت" />
                    <x-form.input name="sort_order" label="ترتیب نمایش" type="number" min="0" :value="$specification?->sort_order ?? 0" required />
                </x-admin.form-section>
                <x-admin.form-section title="رفتار و نمایش" description="این مشخصه در فرم آیتم و فیلترها چگونه استفاده شود؟" icon="ki-filled ki-setting-2">
                    <div><x-form.hidden name="is_required" value="0" /><x-form.toggle name="is_required" label="اجباری برای آیتم" :checked="$specification?->is_required ?? false" /></div>
                    <div><x-form.hidden name="is_highlighted" value="0" /><x-form.toggle name="is_highlighted" label="نمایش در ویژگی‌های برجسته" :checked="$specification?->is_highlighted ?? false" /></div>
                    <div><x-form.hidden name="is_filterable" value="0" /><x-form.toggle name="is_filterable" label="قابل استفاده در فیلترها" :checked="$specification?->is_filterable ?? false" /></div>
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
    @if ($specification)
        <x-card.form
            :action="route('admin.categories.specifications.fields.delete', [$category->id, $specification->id])"
            method="DELETE"
            :submit="['title' => 'حذف مشخصه', 'type' => 'light']"
            class="mt-5"
        >
            <p class="text-sm text-gray-500">مشخصه‌ای که برای آیتم‌ها مقدار دارد قابل حذف نیست.</p>
        </x-card.form>
    @endif
</x-panel-layout>
