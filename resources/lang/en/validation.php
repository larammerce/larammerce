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

    'accepted' => ':attribute نیاز به تایید دارد.',
    'active_url' => ':attribute یک آدرس تایید شده نیست.',
    'after' => ':attribute انتخاب شده باید بعد از :date باشد.',
    'alpha' => ':attribute باید فقط از حروف تشکیل شده باشد.',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num' => 'The :attribute may only contain letters and numbers.',
    'array' => 'The :attribute must be an array.',
    'before' => 'The :attribute must be a date before :date.',
    'between' => [
        'numeric' => ':attribute باید بزرگتر از :min و کوچکتر از :max باشد.',
        'file' => 'The :attribute must be between :min and :max kilobytes.',
        'string' => 'The :attribute must be between :min and :max characters.',
        'array' => 'The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'confirmed' => 'فیلد :attribute با تکرارش مطابقت ندارد.',
    'date' => 'The :attribute is not a valid date.',
    'date_format' => 'The :attribute does not match the format :format.',
    'different' => 'The :attribute and :other must be different.',
    'digits' => 'The :attribute must be :digits digits.',
    'digits_between' => 'The :attribute must be between :min and :max digits.',
    'dimensions' => ':attribute حداقل ابعاد یا نسبت مورد انتظار را ندارد.',
    'distinct' => 'The :attribute field has a duplicate value.',
    'email' => ':attribute معتبر نیست.',
    'exists' => 'این :attribute در سیستم موجود نیست.',
    'file' => 'The :attribute must be a file.',
    'filled' => 'The :attribute field is required.',
    'image' => ':attribute باید تصویر باشد.',
    'in' => 'فیلد :attribute نامعتبر است.',
    'in_array' => 'The :attribute field does not exist in :other.',
    'integer' => ':attribute باید عدد باشد.',
    'ip' => 'The :attribute must be a valid IP address.',
    'json' => 'The :attribute must be a valid JSON string.',
    'max' => [
        'numeric' => 'فیلد :attribute باید کمتر از :min باشد.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => ':attribute حد اکثر میتواند دارای :max کاراکتر باشد.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'فیلد :attribute باید بیشتر از :min باشد.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => ':attribute حد اقل باید دارای :min کاراکتر باشد.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'not_in' => 'The selected :attribute is invalid.',
    'numeric' => 'فیلد :attribute باید عددی باشد.',
    'present' => 'The :attribute field must be present.',
    'regex' => 'فیلد :attribute به شکل درست وارد نشده است.',
    'required' => 'فیلد :attribute الزامی است.',
    //'required_if' => 'فیلد :attribute زمانی که مقدار فیلد :other برابر :value باشد، اجباری است.',
    'required_if' => 'فیلد :attribute الزامی است.',
    'required_unless' => 'The :attribute field is required unless :other is in :values.',
    'required_with' => 'فیلد :attribute الزامی است.',
    'required_with_all' => 'The :attribute field is required when :values is present.',
    'required_without' => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same' => 'The :attribute and :other must match.',
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'file' => 'The :attribute must be :size kilobytes.',
        'string' => 'The :attribute must be :size characters.',
        'array' => 'The :attribute must contain :size items.',
    ],
    'string' => 'The :attribute must be a string.',
    'timezone' => 'The :attribute must be a valid zone.',
    'unique' => 'این :attribute در حال حاضر در سیستم موجود است.',
    'url' => ':attribute یک آدرس معتبر نیست.',
    'recaptcha' => 'لطفا تیک قسمت من ربات نیستم را بزنید.',
    'national_code' => 'لطفا کد ملی معتبر را وارد کنید.',
    'user_alphabet_rule' => 'لطفا از حروف فارسی استفاده کنید.',
    'mobile_number' => 'لطفا شماره‌ی موبایل معتبر وارد کنید.',
    'delivery_period' => 'بازه زمانی انتخاب شده متاسفانه معتبر نمی‌باشد',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specifApp\Providers\Validatoric custom language line for a given attribute rule.
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
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'id' => 'شناسه',
        'name' => 'نام',
        'familyname' => 'نام خانوادگی',
        'title' => 'عنوان',
        'family' => 'نام خانوادگی',
        'username' => 'نام کاربری',
        'email' => 'ایمیل',
        'created_at' => 'تاریخ ساخت',
        'updated_at' => 'تاریخ ویرایش',
        'password' => 'کلمه عبور',
        'password_confirmation' => 'تایید کلمه عبور',
        'identifier' => 'شناساگر',
        'image' => 'تصویر',
        'main_phone' => 'تلفن همراه',
        'phone_number' => 'شماره تماس',
        'superscription' => 'ادامه آدرس',
        'transferee_name' => 'نام دریافت کننده',
        'city_id' => 'شهر',
        'state_id' => 'استان',
        'district_id' => 'نام محله',
        'zipcode' => 'کد پستی',
        'customer_address_id' => 'انتخاب آدرس',
        'shipment_method' => 'انتخاب روش ارسال',
        'payment_type' => 'انتخاب روش پرداخت',
        'current_password' => 'کلمه عبور فعلی',
        'new_password' => 'کلمه عبور جدید',
        'new_password_confirmation' => 'تکرار کلمه عبور جدید',
        'has_paper' => 'همراه با فاکتور',
        'rules_agreement' => 'موافقت با قوانین و مقررات',
        'birthday_year' => 'سال',
        'birthday_month' => 'ماه',
        'birthday_day' => 'روز',
        'national_code' => 'کد ملی',
        'company_name' => 'نام شرکت',
        'economical_code' => 'کد اقتصادی',
        'national_id' => 'شناسه ملی',
        'registration_code' => 'شناسه ثبت',
        'company_phone' => 'تلفن تماس',
        'mobile' => 'تلفن همراه',
        'one_time_code' => 'کد یکبار مصرف',
        'value' => 'مقدار',
        'expiration_date' => 'تاریخ انقضا',
        'discount_code' => 'کد تخفیف',
        'credit' => 'اعتبار',
        'shipment_driver' => 'نحوه‌ی ارسال',
        'shipment_data_tracking_code' => 'کد پیگیری',
        'shipment_data_delivery_date' => 'تاریخ تحویل',
        'message' => 'پیام',
        'subject' => 'موضوع',
        'code' => 'کد',
        'location' => 'محل',
        'ownership' => 'نوع',
        'license' => 'جواز',
        'address' => 'آدرس',
        'attached_file' => 'فایل ضمیمه',
        'cv' => 'فایل رزومه',
        'major' => 'رشته',
        'academy-type' => 'نوع آموزش',
        'home-major' => 'گرایش',
        'special-major' => 'گرایش',
        'academy-level' => 'سطح آموزش',
        'academy-device' => 'دستگاه',
        'supplier' => 'نوع شخص تامین کننده',
        'product_desc' => 'توضیحات محصول',
        'additional_desc' => 'توضیحات بیشتر',
        'company_state_id' => 'استان شرکت',
        'company_city_id' => 'شهر شرکت',
        'phone' => 'تلفن',
        'gender' => 'جنسیت',
        'birth_number' => 'شماره شناسنامه',
        'bank_account_card_number' => 'شماره کارت',
        'bank_account_uuid' => 'شماره شبا',
        'default_delay_hours' => 'ساعت تاخیر ارسال پرسشنامه',
        'default_delay_days' => 'روزهای تاخیر ارسال پرسشنامه',
        'default_survey_url' => 'آدرس وب‌‌پیج فرم پرسشنامه',
        'descent_percentage' => 'درصد کاهش قیمت',
        'delivery_period' => "زمان ارسال",
        'representative_type' => 'نوع آشنایی با سیستم',
    ],

];
