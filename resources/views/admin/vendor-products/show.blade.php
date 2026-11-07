<x-panel-layout
    :title="'بررسی محصول فروشنده #' . $vendorProduct->id"
    sub-title="مشخصات کالا و فروشنده را بررسی و وضعیت انتشار را تعیین کنید."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <div class="mb-5 flex justify-end">
        <x-button href="{{ route('admin.vendor-products.index') }}" variant="light">بازگشت به فهرست</x-button>
    </div>

    <x-card title="مشخصات محصول و فروشنده">
        <dl class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-sm text-gray-500">نام محصول</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $vendorProduct->product->item->name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">فروشنده</dt>
                <dd class="mt-1 font-semibold text-gray-900">{{ $vendorProduct->vendor->store_name ?: $vendorProduct->vendor->company_name }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">شماره تماس فروشنده</dt>
                <dd class="mt-1 text-gray-700">{{ $vendorProduct->vendor->user->phone }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">قیمت فروش</dt>
                <dd class="mt-1 text-gray-700">{{ number_format($vendorProduct->effectivePrice($vendorProduct->product)) }} تومان</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">موجودی</dt>
                <dd class="mt-1 text-gray-700">{{ number_format($vendorProduct->stock) }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">هزینه ارسال</dt>
                <dd class="mt-1 text-gray-700">{{ number_format($vendorProduct->shipping_price) }} تومان</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">رنگ و سایز</dt>
                <dd class="mt-1 text-gray-700">{{ $vendorProduct->product->color?->name ?? '—' }} / {{ $vendorProduct->product->size?->value ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">وضعیت بررسی</dt>
                <dd class="mt-1"><x-badge :variant="$vendorProduct->approval_status->color()" size="sm" dot>{{ $vendorProduct->approval_status->trans() }}</x-badge></dd>
            </div>
            <div>
                <dt class="text-sm text-gray-500">تاریخ ثبت</dt>
                <dd class="mt-1 text-gray-700">{{ jalali_date($vendorProduct->created_at, 'Y/m/d H:i') }}</dd>
            </div>
        </dl>

        @if ($vendorProduct->rejection_reason)
            <div class="mt-5 rounded-lg border border-danger/20 bg-danger-light p-4">
                <h3 class="text-sm font-semibold text-danger">دلیل رد قبلی</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-gray-700">{{ $vendorProduct->rejection_reason }}</p>
            </div>
        @endif

        @if ($vendorProduct->product->images->isNotEmpty())
            <div class="mt-5 flex flex-wrap gap-3">
                @foreach ($vendorProduct->product->images as $image)
                    <img class="h-24 w-24 rounded-lg object-cover" src="{{ public_image_url($image->image) }}" alt="تصویر {{ $vendorProduct->product->item->name }}">
                @endforeach
            </div>
        @endif
    </x-card>

    <x-card.form
        :action="route('admin.vendor-products.approval.update', $vendorProduct->id)"
        method="PATCH"
        class="admin-editor-form mt-5"
        :cancel="['title' => 'بازگشت به فهرست', 'type' => 'light', 'to' => route('admin.vendor-products.index')]"
        :submit="['title' => 'ثبت تصمیم', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
    >
        <x-admin.form-intro title="تعیین نتیجه بررسی" description="محصول فقط پس از تأیید در فروشگاه قابل مشاهده و خرید است." icon="ki-filled ki-shield-tick" />
        <div class="admin-form-layout admin-form-layout--single">
            <div class="admin-form-main">
                <x-admin.form-section title="تصمیم مدیر" description="برای رد محصول، دلیل مشخصی بنویسید تا فروشنده بداند چه چیزی را اصلاح کند." icon="ki-filled ki-message-text-2">
                    <x-form.select
                        name="approval_status"
                        label="نتیجه بررسی"
                        :options="$approvalOptions"
                        :selected="$selectedApprovalStatus"
                        placeholder="یک گزینه انتخاب کنید"
                        required
                    />
                    <x-form.textarea
                        name="rejection_reason"
                        label="دلیل رد"
                        :value="$vendorProduct->rejection_reason"
                        rows="4"
                        placeholder="در صورت رد، دلیل را وارد کنید"
                        hint="هنگام انتخاب «ردشده» این فیلد الزامی است."
                    />
                </x-admin.form-section>
            </div>
        </div>
    </x-card.form>
</x-panel-layout>
