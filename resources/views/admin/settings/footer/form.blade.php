<x-panel-layout :title="($entry ? 'ویرایش ' : 'افزودن ').$kindTitle" sub-title="عنوان، مقصد، تصویر و ترتیب نمایش این بخش از فوتر را تعیین کنید.">
    <x-card.form
        :action="$entry ? route('admin.footer.update', [$kind, $entry->id]) : route('admin.footer.store', $kind)"
        :method="$entry ? 'PUT' : 'POST'"
        enctype="multipart/form-data"
        class="admin-editor-form"
        :cancel="['title' => 'بازگشت', 'type' => 'light', 'to' => route('admin.footer.index', $kind)]"
        :submit="['title' => 'ذخیره', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
    >
        <x-admin.form-intro :title="$kindTitle" description="هر آیتم را جداگانه ذخیره کنید تا مدیریت آن آسان باشد." icon="ki-filled ki-menu" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="محتوا" description="متن و مقصدی که کاربر در فوتر می‌بیند." icon="ki-filled ki-text" :two-columns="true">
                    @if (in_array($kind, ['features', 'groups'], true))
                        <x-form.input name="title" label="عنوان" :value="$entry?->title" required />
                    @else
                        <x-form.input name="label" label="عنوان لینک" :value="$entry?->label" required />
                    @endif

                    @if ($kind === 'links')
                        <x-form.select name="footer_link_group_id" label="ستون لینک" :options="$groupOptions" :selected="$entry?->footer_link_group_id" placeholder="انتخاب ستون" required />
                    @endif

                    @if ($kind !== 'groups')
                        <x-form.input name="url" label="پیوند مقصد" :value="$entry?->url" dir="ltr" placeholder="/shop یا https://example.com" :required="in_array($kind, ['links', 'social', 'apps'], true)" />
                    @endif

                    @if (in_array($kind, ['features', 'social'], true))
                        <x-form.input name="icon" label="کلاس آیکون" :value="$entry?->icon" dir="ltr" placeholder="ki-filled ki-delivery" hint="اگر تصویر بارگذاری کنید، تصویر به‌جای آیکون نمایش داده می‌شود." />
                    @endif

                    @if (in_array($kind, ['features', 'social', 'apps'], true))
                        <div class="grid gap-2">
                            <x-form.file name="image" label="تصویر یا نشان" accept="image/*" :current-image="$entry?->image_path ? public_image_url($entry->image_path) : null" />
                            @if ($entry?->image_path)<x-form.checkbox name="remove_image" label="حذف تصویر فعلی" />@endif
                        </div>
                    @endif

                    @if ($kind === 'links')
                        <x-form.hidden name="opens_in_new_tab" value="0" />
                        <x-form.checkbox name="opens_in_new_tab" label="باز شدن در تب جدید" :checked="$entry?->opens_in_new_tab ?? false" />
                    @endif
                </x-admin.form-section>
            </div>

            <aside class="admin-form-sidebar">
                <x-admin.form-section title="نمایش" description="جایگاه آیتم در فوتر" icon="ki-filled ki-eye" compact>
                    <x-form.input name="sort_order" type="number" min="0" max="65535" label="ترتیب نمایش" :value="$entry?->sort_order ?? 0" required />
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$entry?->status?->value ?? \App\Enums\GlobalStatusEnum::ACTIVE->value" required />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
