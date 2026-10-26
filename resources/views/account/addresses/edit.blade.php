<x-user.account-shell title="ویرایش نشانی" sub-title="اطلاعات گیرنده و محل تحویل را به‌روزرسانی کنید." active="addresses">
    <div class="user-address-form__intro">
        <span aria-hidden="true"><i class="ki-filled ki-geolocation"></i></span>
        <p>این نشانی هنگام انتخاب محل تحویل سفارش در دسترس شما خواهد بود.</p>
    </div>
    <div class="user-address-form">
        <x-card.form
            :action="route('account.addresses.update', $address->id)"
            method="PUT"
            title="مشخصات نشانی"
            :cancel="['title' => 'بازگشت به نشانی‌ها', 'type' => 'light', 'to' => route('account.addresses.index')]"
            :submit="['title' => 'ذخیره تغییرات', 'type' => 'primary', 'icon' => 'ki-filled ki-check']"
        >
            <div class="user-address-form__fields">
                <x-form.input name="recipient_name" label="نام گیرنده" :value="$address->recipient_name" autocomplete="name" icon="ki-filled ki-profile-circle" required />
                <x-form.input name="phone_number" type="tel" label="شماره تماس گیرنده" :value="$address->phone_number" dir="ltr" inputmode="tel" autocomplete="tel" icon="ki-filled ki-phone" required />

                <div class="user-address-form__locations" data-address-form>
                    <x-form.select
                        name="province_id"
                        label="استان"
                        :options="$provinceOptions"
                        :selected="$address->province_id"
                        placeholder="استان را انتخاب کنید"
                        required
                        data-address-province
                    />
                    <x-form.select
                        name="city_id"
                        label="شهر"
                        :options="$cityOptions"
                        :selected="$address->city_id"
                        placeholder="ابتدا استان را انتخاب کنید"
                        required
                        data-address-city
                    />
                </div>

                <x-form.input
                    name="recipient_national_code"
                    label="کد ملی گیرنده"
                    :value="$address->recipient_national_code"
                    dir="ltr"
                    inputmode="numeric"
                    autocomplete="off"
                    icon="ki-filled ki-profile-circle"
                    hint="اختیاری"
                />
                <x-form.textarea
                    name="full_address"
                    label="نشانی کامل"
                    :value="$address->full_address"
                    placeholder="خیابان، کوچه، پلاک و واحد را وارد کنید"
                    rows="4"
                    required
                />
            </div>
        </x-card.form>
    </div>
</x-user.account-shell>

@include('account.addresses.partials.location-select-script')
