<x-panel-layout title="ویرایش بخش فروشگاه" sub-title="مشخصات ظاهری بخش و تصاویر آن را ویرایش کنید.">
    <x-card.form
        action="{{ route('admin.sections.update', $section->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        id="edit_section_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.sections.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="بخش فروشگاه" description="نام و آیتم‌های بخش را ثبت کنید؛ رنگ، تصاویر و وضعیت نمایش در کارت‌های جدا هستند." icon="ki-filled ki-category" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="نام و شناسه" description="عنوان و نشانی صفحهٔ این بخش" icon="ki-filled ki-text" :two-columns="true">
                    <x-form.input name="name" label="نام بخش" :value="$section->name" required />
                    <x-form.input name="slug" label="شناسه متنی (Slug)" :value="$section->slug" dir="ltr" required />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات بخش">
                <x-admin.form-section title="ظاهر صفحهٔ بخش" description="رنگ و تصویر هدر این بخش" icon="ki-filled ki-color-swatch">
                    <x-form.input name="bg_color" type="color" label="رنگ پس‌زمینه" :value="$section->bg_color ?? '#ffffff'" />
                    <x-form.file
                        name="bg_image"
                        label="تصویر پس‌زمینه"
                        accept="image/*"
                        :current-image="$section->bg_image ? public_image_url($section->bg_image) : null"
                        hint="برای نگه‌داشتن تصویر فعلی، فایل جدید انتخاب نکنید."
                        class="sm:col-span-2"
                    />
                    @if ($section->bg_image)
                    <x-form.checkbox
                        name="remove_bg_image"
                        value="1"
                        label="حذف تصویر پس‌زمینه فعلی هنگام ذخیره"
                        hint="اگر تصویر جدید بارگذاری شود، تصویر جدید جایگزین می‌شود."
                        class="sm:col-span-2"
                    />
                    @endif
                </x-admin.form-section>
                <x-admin.form-section title="نمایش" description="وضعیت بخش در فروشگاه" icon="ki-filled ki-eye">
                    <x-form.select name="status" label="وضعیت" :options="$statusOptions" :selected="$section->status->value" required />
                </x-admin.form-section>
            </aside>
            <div class="admin-form-main col-span-full">
                <x-admin.form-section title="آیتم‌های بخش" description="آیتم‌هایی را انتخاب کنید که در این بخش نشان داده می‌شوند." icon="ki-filled ki-package">
                    <livewire:admin.sections.section-item-picker :selected-item-ids="old('item_ids', old('item_ids_present') ? [] : $section->items->pluck('id')->all()) ?? []" />
                </x-admin.form-section>
                <x-admin.form-section title="گالری بخش" description="تصاویر گالری را در جایگاه‌های جدا اضافه کنید." icon="ki-filled ki-picture">
                    <x-form.image-gallery name="images" :existing-count="$section->images->count()" class="sm:col-span-2" />
                    @error('images') <p class="text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                    @error('images.*') <p class="text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                </x-admin.form-section>
                @if ($section->images->isNotEmpty())
                    <x-admin.form-section title="تصاویر فعلی بخش" description="تصاویر انتخاب‌شده هنگام ذخیره از گالری حذف می‌شوند." icon="ki-filled ki-picture">
                        <div class="grid min-w-0 grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($section->images as $sectionImage)
                                <div class="rounded-xl border border-gray-200 p-2 dark:border-gray-700">
                                    <img src="{{ public_image_url($sectionImage->image) }}" alt="تصویر {{ $section->name }}" class="h-24 w-full rounded-lg object-cover" loading="lazy" />
                                    <x-form.checkbox name="delete_image_ids[{{ $sectionImage->id }}]" :value="$sectionImage->id" label="حذف هنگام ذخیره" class="mt-2" />
                                </div>
                            @endforeach
                        </div>
                        @error('delete_image_ids') <p class="mt-3 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                        @error('delete_image_ids.*') <p class="mt-3 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                        <p class="mt-3 text-xs text-gray-500">تصاویر انتخاب‌شده پس از ذخیره از گالری و فضای ذخیره‌سازی پاک می‌شوند.</p>
                    </x-admin.form-section>
                @endif
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
