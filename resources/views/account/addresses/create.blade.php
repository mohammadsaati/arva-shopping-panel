<x-user.account-shell title="افزودن نشانی" sub-title="مشخصات گیرنده و محل تحویل سفارش را وارد کنید." active="addresses">
    <div class="user-address-form__intro">
        <span aria-hidden="true"><i class="ki-filled ki-geolocation"></i></span>
        <p>برای تحویل دقیق‌تر، استان و شهر را انتخاب کنید و نشانی را همراه با پلاک و واحد بنویسید.</p>
    </div>
    <div class="user-address-form">
        <x-card.form
            :action="route('account.addresses.store', $returnToCheckout ? ['return_to' => 'checkout'] : [])"
            title="مشخصات نشانی"
            :cancel="['title' => $returnToCheckout ? 'بازگشت به سفارش' : 'بازگشت به نشانی‌ها', 'type' => 'light', 'to' => $returnToCheckout ? route('checkout.show') : route('account.addresses.index')]"
            :submit="['title' => 'ثبت نشانی', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
        >
            <div class="user-address-form__fields">
                <x-form.input name="recipient_name" label="نام گیرنده" autocomplete="name" icon="ki-filled ki-profile-circle" required />
                <x-form.input name="phone_number" type="tel" label="شماره تماس گیرنده" dir="ltr" inputmode="tel" autocomplete="tel" icon="ki-filled ki-phone" required />

                <div class="user-address-form__locations" data-address-form>
                    <x-form.select
                        name="province_id"
                        label="استان"
                        :options="$provinceOptions"
                        placeholder="استان را انتخاب کنید"
                        required
                        data-address-province
                    />
                    <x-form.select
                        name="city_id"
                        label="شهر"
                        :options="$cityOptions"
                        placeholder="ابتدا استان را انتخاب کنید"
                        required
                        data-address-city
                    />
                </div>

                <x-form.input
                    name="recipient_national_code"
                    label="کد ملی گیرنده"
                    dir="ltr"
                    inputmode="numeric"
                    autocomplete="off"
                    icon="ki-filled ki-profile-circle"
                    hint="اختیاری"
                />
                <x-form.textarea
                    name="full_address"
                    label="نشانی کامل"
                    placeholder="خیابان، کوچه، پلاک و واحد را وارد کنید"
                    rows="4"
                    required
                />
            </div>
        </x-card.form>
    </div>
</x-user.account-shell>

@include('account.addresses.partials.location-select-script')
