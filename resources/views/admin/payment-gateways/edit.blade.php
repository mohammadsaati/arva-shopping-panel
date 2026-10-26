<x-panel-layout
    title="تنظیمات {{ $gateway->name }}"
    sub-title="وضعیت درگاه و اطلاعات اتصال به سرویس پرداخت را اینجا وارد کنید."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <x-card.form
        action="{{ route('admin.payment-gateways.update', $gateway->id) }}"
        method="PUT"
        enctype="multipart/form-data"
        class="admin-editor-form"
        :cancel="['title' => 'بازگشت به فهرست', 'type' => 'light', 'to' => route('admin.payment-gateways.index')]"
        :submit="['title' => 'ذخیره تنظیمات', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
    >
        <x-admin.form-intro
            title="اتصال درگاه"
            description="تا وقتی کد پذیرنده معتبر نباشد، این درگاه را فعال نکنید. خریدار فقط درگاه‌های فعال را در تسویه‌حساب می‌بیند."
            icon="ki-filled ki-wallet"
        />

        <div class="admin-form-layout">
            <div class="admin-form-main">
                <x-admin.form-section title="شناسه درگاه" description="نام نمایشی برای خریدار و پنل؛ نوع درگاه از کد مشخص می‌شود." icon="ki-filled ki-text">
                    <x-form.input name="name" label="نام نمایشی" :value="$gateway->name" required />
                    <x-form.input name="driver_label" label="نوع درگاه" :value="$gateway->driver->trans()" readonly />
                </x-admin.form-section>

                <x-admin.form-section title="تصویر درگاه" description="این تصویر در صفحه تسویه‌حساب کنار نام درگاه دیده می‌شود. اگر خالی بماند، نشان پیش‌فرض همان درگاه استفاده می‌شود." icon="ki-filled ki-picture">
                    <x-form.file
                        name="image"
                        label="لوگو یا نشان درگاه"
                        accept="image/*"
                        :current-image="$gateway->image ? public_image_url($gateway->image) : null"
                        hint="اختیاری؛ حداکثر ۲ مگابایت. فرمت تصویر."
                    />
                    @if ($gateway->image)
                        <x-form.checkbox name="remove_image" value="1" label="حذف تصویر فعلی هنگام ذخیره" />
                    @endif
                </x-admin.form-section>

                @if ($gateway->driver === \App\Enums\PaymentGatewayDriverEnum::ZARINPAL)
                    <x-admin.form-section title="تنظیمات زرین‌پال" description="کد پذیرنده ۳۶ کاراکتری را از پنل زرین‌پال بردارید. برای تست از حالت آزمایشی استفاده کنید." icon="ki-filled ki-credit-cart" :two-columns="true">
                        <x-form.input
                            name="merchant_id"
                            label="کد پذیرنده"
                            :value="$gateway->merchantId()"
                            dir="ltr"
                            placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                            hint="همان merchant_id سی‌وشش کاراکتری زرین‌پال است."
                        />
                        <x-form.select
                            name="currency"
                            label="واحد مبلغ ارسالی"
                            :options="$currencyOptions"
                            :selected="$gateway->currency()->value"
                            required
                            hint="قیمت‌های فروشگاه به تومان است؛ معمولاً تومان (IRT) را انتخاب کنید."
                        />
                        <x-form.hidden name="sandbox" value="0" />
                        <x-form.toggle
                            name="sandbox"
                            label="حالت آزمایشی (Sandbox)"
                            :checked="old('sandbox', $gateway->usesSandbox())"
                            hint="در حالت آزمایشی درخواست به sandbox.zarinpal.com می‌رود."
                        />
                    </x-admin.form-section>
                @endif
            </div>

            <aside class="admin-form-sidebar">
                <x-admin.form-section title="انتشار" description="فقط درگاه فعال در صفحه تسویه‌حساب دیده می‌شود." icon="ki-filled ki-check" compact>
                    <x-form.select
                        name="status"
                        label="وضعیت"
                        :options="$statusOptions"
                        :selected="$gateway->status->value"
                        required
                    />
                </x-admin.form-section>
            </aside>
        </div>
    </x-card.form>
</x-panel-layout>
