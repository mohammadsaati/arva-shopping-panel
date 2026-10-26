<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'پذیرش :attribute الزامی است.',
    'accepted_if' => 'وقتی :other برابر :value است، پذیرش :attribute الزامی است.',
    'active_url' => 'آدرس :attribute معتبر نیست.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی بعد از یا برابر :date باشد.',
    'alpha' => ':attribute فقط می‌تواند شامل حروف باشد.',
    'alpha_dash' => ':attribute فقط می‌تواند شامل حروف، عدد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute فقط می‌تواند شامل حروف و عدد باشد.',
    'any_of' => 'مقدار :attribute معتبر نیست.',
    'array' => ':attribute باید آرایه باشد.',
    'array_keys' => ':attribute فقط می‌تواند شامل کلیدهای :values باشد.',
    'ascii' => 'قالب :attribute معتبر نیست.',
    'base64' => 'رشته Base64 برای :attribute معتبر نیست.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی قبل از یا برابر :date باشد.',
    'between' => [
        'array' => 'تعداد :attribute باید بین :min تا :max مورد باشد.',
        'file' => 'حجم :attribute باید بین :min تا :max کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute باید بین :min تا :max باشد.',
        'string' => 'طول :attribute باید بین :min تا :max نویسه باشد.',
    ],
    'boolean' => ':attribute باید درست یا نادرست باشد.',
    'can' => 'مقدار :attribute مجاز نیست.',
    'confirmed' => 'تأیید :attribute با مقدار واردشده یکسان نیست.',
    'contains' => 'مقدار لازم برای :attribute وارد نشده است.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => 'تاریخ :attribute معتبر نیست.',
    'date_equals' => ':attribute باید برابر :date باشد.',
    'date_format' => 'قالب :attribute باید مطابق :format باشد.',
    'decimal' => ':attribute باید :decimal رقم اعشار داشته باشد.',
    'declined' => 'رد :attribute الزامی است.',
    'declined_if' => 'وقتی :other برابر :value است، رد :attribute الزامی است.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min تا :max رقم باشد.',
    'dimensions' => 'ابعاد تصویر :attribute معتبر نیست.',
    'distinct' => 'مقدار تکراری برای :attribute وارد شده است.',
    'doesnt_contain' => ':attribute نباید شامل :values باشد.',
    'doesnt_end_with' => ':attribute نباید با :values پایان یابد.',
    'doesnt_start_with' => ':attribute نباید با :values شروع شود.',
    'email' => 'فرمت :attribute معتبر نیست.',
    'encoding' => 'رمزگذاری :attribute باید :encoding باشد.',
    'ends_with' => ':attribute باید با یکی از :values پایان یابد.',
    'enum' => 'مقدار انتخاب‌شده برای :attribute معتبر نیست.',
    'exists' => 'مقدار انتخاب‌شده برای :attribute معتبر نیست.',
    'extensions' => 'پسوند :attribute باید یکی از :values باشد.',
    'file' => ':attribute باید فایل باشد.',
    'filled' => 'مقدار :attribute الزامی است.',
    'gt' => [
        'array' => 'تعداد :attribute باید بیشتر از :value باشد.',
        'file' => 'حجم :attribute باید بیشتر از :value کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute باید بیشتر از :value باشد.',
        'string' => 'طول :attribute باید بیشتر از :value نویسه باشد.',
    ],
    'gte' => [
        'array' => 'تعداد :attribute باید حداقل :value مورد باشد.',
        'file' => 'حجم :attribute باید حداقل :value کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute باید حداقل :value باشد.',
        'string' => 'طول :attribute باید حداقل :value نویسه باشد.',
    ],
    'hex_color' => 'رنگ :attribute معتبر نیست.',
    'image' => ':attribute باید تصویر باشد.',
    'in' => 'مقدار انتخاب‌شده برای :attribute معتبر نیست.',
    'in_array' => ':attribute باید در :other وجود داشته باشد.',
    'in_array_keys' => ':attribute باید حداقل یکی از کلیدهای :values را داشته باشد.',
    'integer' => ':attribute باید عدد صحیح باشد.',
    'ip' => 'آدرس IP برای :attribute معتبر نیست.',
    'ipv4' => 'آدرس IPv4 برای :attribute معتبر نیست.',
    'ipv6' => 'آدرس IPv6 برای :attribute معتبر نیست.',
    'json' => 'JSON برای :attribute معتبر نیست.',
    'list' => ':attribute باید فهرست باشد.',
    'lowercase' => ':attribute باید با حروف کوچک باشد.',
    'lt' => [
        'array' => 'تعداد :attribute باید کمتر از :value باشد.',
        'file' => 'حجم :attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute باید کمتر از :value باشد.',
        'string' => 'طول :attribute باید کمتر از :value نویسه باشد.',
    ],
    'lte' => [
        'array' => 'تعداد :attribute نباید بیشتر از :value باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :value کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute نباید بیشتر از :value باشد.',
        'string' => 'طول :attribute نباید بیشتر از :value نویسه باشد.',
    ],
    'mac_address' => 'آدرس MAC برای :attribute معتبر نیست.',
    'max' => [
        'array' => 'تعداد :attribute نباید بیشتر از :max باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute نباید بیشتر از :max باشد.',
        'string' => 'طول :attribute نباید بیشتر از :max نویسه باشد.',
    ],
    'max_digits' => 'تعداد ارقام :attribute نباید بیشتر از :max باشد.',
    'mimes' => 'نوع فایل :attribute باید یکی از :values باشد.',
    'mimetypes' => 'نوع فایل :attribute باید یکی از :values باشد.',
    'min' => [
        'array' => 'تعداد :attribute باید حداقل :min مورد باشد.',
        'file' => 'حجم :attribute باید حداقل :min کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute باید حداقل :min باشد.',
        'string' => 'حداقل طول :attribute باید :min نویسه باشد.',
    ],
    'min_digits' => 'تعداد ارقام :attribute باید حداقل :min باشد.',
    'missing' => 'فیلد :attribute نباید ارسال شود.',
    'missing_if' => 'وقتی :other برابر :value است، :attribute نباید ارسال شود.',
    'missing_unless' => 'مگر اینکه :other برابر :value باشد، :attribute نباید ارسال شود.',
    'missing_with' => 'وقتی :values وجود دارد، :attribute نباید ارسال شود.',
    'missing_with_all' => 'وقتی :values وجود دارند، :attribute نباید ارسال شود.',
    'multiple_of' => ':attribute باید مضرب :value باشد.',
    'not_in' => 'مقدار انتخاب‌شده برای :attribute معتبر نیست.',
    'not_regex' => 'قالب :attribute معتبر نیست.',
    'numeric' => ':attribute باید عدد باشد.',
    'password' => [
        'letters' => ':attribute باید حداقل یک حرف داشته باشد.',
        'mixed' => ':attribute باید حروف بزرگ و کوچک داشته باشد.',
        'numbers' => ':attribute باید حداقل یک عدد داشته باشد.',
        'symbols' => ':attribute باید حداقل یک نماد داشته باشد.',
        'uncompromised' => ':attribute در فهرست رمزهای افشاشده دیده شده؛ رمز دیگری انتخاب کنید.',
    ],
    'present' => 'فیلد :attribute باید ارسال شود.',
    'present_if' => 'وقتی :other برابر :value است، :attribute باید ارسال شود.',
    'present_unless' => 'مگر اینکه :other برابر :value باشد، :attribute باید ارسال شود.',
    'present_with' => 'وقتی :values وجود دارد، :attribute باید ارسال شود.',
    'present_with_all' => 'وقتی :values وجود دارند، :attribute باید ارسال شود.',
    'prohibited' => 'فیلد :attribute مجاز نیست.',
    'prohibited_if' => 'وقتی :other برابر :value است، :attribute مجاز نیست.',
    'prohibited_if_accepted' => 'وقتی :other پذیرفته شده، :attribute مجاز نیست.',
    'prohibited_if_declined' => 'وقتی :other رد شده، :attribute مجاز نیست.',
    'prohibited_unless' => 'مگر اینکه :other در :values باشد، :attribute مجاز نیست.',
    'prohibits' => 'وجود :attribute مانع ارسال :other می‌شود.',
    'regex' => 'قالب :attribute معتبر نیست.',
    'required' => 'فیلد :attribute الزامی است.',
    'required_array_keys' => ':attribute باید شامل موارد :values باشد.',
    'required_if' => 'وقتی :other برابر :value است، :attribute الزامی است.',
    'required_if_accepted' => 'وقتی :other پذیرفته شده، :attribute الزامی است.',
    'required_if_declined' => 'وقتی :other رد شده، :attribute الزامی است.',
    'required_unless' => 'مگر اینکه :other در :values باشد، :attribute الزامی است.',
    'required_with' => 'وقتی :values وجود دارد، :attribute الزامی است.',
    'required_with_all' => 'وقتی :values وجود دارند، :attribute الزامی است.',
    'required_without' => 'وقتی :values وجود ندارد، :attribute الزامی است.',
    'required_without_all' => 'وقتی هیچ‌کدام از :values وجود ندارند، :attribute الزامی است.',
    'same' => ':attribute باید با :other یکسان باشد.',
    'size' => [
        'array' => 'تعداد :attribute باید :size مورد باشد.',
        'file' => 'حجم :attribute باید :size کیلوبایت باشد.',
        'numeric' => 'مقدار :attribute باید :size باشد.',
        'string' => 'طول :attribute باید :size نویسه باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از :values شروع شود.',
    'string' => ':attribute باید متن باشد.',
    'timezone' => 'منطقه زمانی :attribute معتبر نیست.',
    'unique' => ':attribute قبلاً ثبت شده است.',
    'uploaded' => 'بارگذاری :attribute انجام نشد.',
    'uppercase' => ':attribute باید با حروف بزرگ باشد.',
    'url' => 'آدرس :attribute معتبر نیست.',
    'ulid' => 'ULID برای :attribute معتبر نیست.',
    'uuid' => 'UUID برای :attribute معتبر نیست.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'phone' => 'شماره موبایل',
        'password' => 'رمز عبور',
        'password_confirmation' => 'تکرار رمز عبور',
        'current_password' => 'رمز عبور فعلی',
        'first_name' => 'نام',
        'last_name' => 'نام خانوادگی',
        'email' => 'ایمیل',
        'recovery_email' => 'ایمیل بازیابی',
        'address_id' => 'نشانی تحویل',
        'payment_gateway_id' => 'روش پرداخت',
        'discount_code' => 'کد تخفیف',
        'quantity' => 'تعداد',
        'province_id' => 'استان',
        'city_id' => 'شهر',
        'full_address' => 'نشانی کامل',
        'phone_number' => 'شماره تماس',
        'recipient_name' => 'نام گیرنده',
        'recipient_national_code' => 'کد ملی گیرنده',
        'rating' => 'امتیاز',
        'title' => 'عنوان',
        'body' => 'متن',
        'guest_name' => 'نام نمایشی',
        'guest_email' => 'ایمیل',
        'token' => 'لینک بازیابی',
        'Authority' => 'شناسه پرداخت',
        'Status' => 'وضعیت پرداخت',
    ],

];
