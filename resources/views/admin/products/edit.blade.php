<x-panel-layout
    title="ویرایش محصول"
    sub-title="مشخصات محصول و آیتم مرتبط را ویرایش کنید."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex flex-wrap justify-end gap-2">
        <x-button href="{{ route('admin.items.edit', $product->item_id) }}" variant="light" icon="ki-filled ki-setting-2">
            ویرایش مشخصات کالا
        </x-button>
        <x-button href="{{ route('admin.categories.specifications.index', $product->item->category_id) }}" variant="light" icon="ki-filled ki-category">
            مدیریت فیلدهای دسته‌بندی
        </x-button>
    </div>

    <x-card.form
        action="{{ route('admin.products.update', $product->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        id="edit_product_form"
        class="admin-editor-form"
        :cancel="['title' => 'انصراف', 'type' => 'light', 'to' => route('admin.products.index')]"
        :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary']"
    >
        <x-admin.form-intro title="ویرایش محصول" description="تنوع، قیمت، ارسال و گالری را در کارت‌های جدا به‌روزرسانی کنید." icon="ki-filled ki-package" />
        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="آیتم و تنوع" description="ارتباط محصول با آیتم، رنگ و سایز" icon="ki-filled ki-category" :two-columns="true">
            <x-form.select2
                name="item_id"
                label="آیتم مرتبط"
                :options="$items"
                :selected="$product->item_id"
                :route="route('admin.items.select-options')"
                placeholder="انتخاب آیتم"
                hint="نام یا کد آیتم را جستجو کنید."
                :clearable="false"
                required
            />
            <x-form.select name="color_id" label="رنگ" :options="$colors" :selected="$product->color_id" placeholder="بدون رنگ" />
            <x-form.select name="size_id" label="سایز" :options="$sizes" :selected="$product->size_id" placeholder="بدون سایز" />
                </x-admin.form-section>
                <x-admin.form-section title="قیمت و موجودی" description="مبالغ و تعداد قابل فروش" icon="ki-filled ki-price-tag" :two-columns="true">
            <x-form.input name="price" label="قیمت (تومان)" type="number" min="0" :value="$product->price" dir="ltr" required />
            <x-form.input name="stock" label="موجودی" type="number" min="0" :value="$product->stock" dir="ltr" required />
            <x-form.input name="shipping_price" label="هزینه ارسال پیش‌فرض (تومان)" type="number" min="0" :value="$product->shipping_price" dir="ltr" required />
                </x-admin.form-section>
                <x-admin.form-section title="هزینه ارسال شهرها" description="نرخ‌های ویژه برای شهرهای مشخص" icon="ki-filled ki-delivery-3">
                    <x-admin.product-city-rates :city-options="$cities" :rates="$product->cityRates->map(fn ($rate) => ['city_id' => $rate->city_id, 'shipping_price' => $rate->shipping_price])->all()" />
                </x-admin.form-section>
            </div>
            <aside class="admin-form-sidebar" aria-label="تنظیمات محصول">
                <x-admin.form-section title="نمایش محصول" description="وضعیت نمایش و فروش" icon="ki-filled ki-eye">
            <x-form.select
                name="status"
                label="وضعیت"
                :options="$statusOptions"
                :selected="$product->status->value"
                required
            />
                </x-admin.form-section>
                <x-admin.form-section title="افزودن تصویر" description="تصاویر جدید این محصول" icon="ki-filled ki-picture">
                    <x-form.file name="images" label="افزودن تصاویر جدید" accept="image/*" multiple hint="حداکثر ۱۰ تصویر برای هر محصول، هر تصویر تا ۵ مگابایت." />
                    @error('images.*') <p class="mt-1 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                </x-admin.form-section>
            </aside>
        </div>
        @if ($product->images->isNotEmpty())
            <x-admin.form-section title="تصاویر فعلی محصول" description="برای هر تصویر می‌توانید جایگزین انتخاب کنید یا حذف آن را هنگام ذخیره مشخص کنید." icon="ki-filled ki-picture">
                <x-datatable title="تصاویر فعلی">
                    <x-slot:head>
                        <x-datatable-heading>تصویر</x-datatable-heading>
                        <x-datatable-heading>جایگزینی</x-datatable-heading>
                        <x-datatable-heading>حذف</x-datatable-heading>
                    </x-slot:head>
                    @foreach ($product->images as $image)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="px-5 py-3"><img src="{{ public_image_url($image->image) }}" alt="تصویر {{ $loop->iteration }} از محصول" class="h-16 w-16 rounded-lg object-cover"></td>
                            <td class="min-w-64 px-5 py-3"><x-form.file name="replacements[{{ $image->id }}]" label="جایگزینی تصویر {{ $loop->iteration }}" accept="image/*" /></td>
                            <td class="px-5 py-3"><x-form.checkbox name="delete_image_ids[{{ $image->id }}]" :value="$image->id" label="حذف هنگام ذخیره" /></td>
                        </tr>
                    @endforeach
                </x-datatable>
                @error('replacements.*') <p class="mt-1 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
                @error('delete_image_ids.*') <p class="mt-1 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
            </x-admin.form-section>
        @endif
    </x-card.form>
</x-panel-layout>
