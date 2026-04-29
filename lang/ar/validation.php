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

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما يكون :other هو :value.',
    'active_url' => 'حقل :attribute ليس رابطًا صحيحًا.',
    'after' => 'يجب أن يكون حقل :attribute تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا لاحقًا أو مطابقًا للتاريخ :date.',
    'alpha' => 'يجب أن يحتوي حقل :attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'يجب أن يحتوي حقل :attribute على أحرف وأرقام فقط.',
    'array' => 'يجب أن يكون حقل :attribute مصفوفة.',
    'before' => 'يجب أن يكون حقل :attribute تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => 'يجب أن يكون حقل :attribute تاريخًا سابقًا أو مطابقًا للتاريخ :date.',
    'between' => [
        'array' => 'يجب أن يحتوي حقل :attribute على عناصر تتراوح بين :min و :max.',
        'file' => 'يجب أن يكون حجم الملف :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول النص :attribute بين :min و :max حرفًا/أحرف.',
    ],
    'boolean' => 'يجب أن تكون قيمة حقل :attribute إما true أو false.',
    'confirmed' => 'تأكيد حقل :attribute غير متطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute ليس تاريخًا صحيحًا.',
    'date_equals' => 'يجب أن يكون حقل :attribute تاريخًا مطابقًا للتاريخ :date.',
    'date_format' => 'لا يتوافق حقل :attribute مع الصيغة :format.',
    'declined' => 'يجب رفض حقل :attribute.',
    'declined_if' => 'يجب رفض حقل :attribute عندما يكون :other هو :value.',
    'different' => 'يجب أن يكون حقلا :attribute و :other مختلفين.',
    'digits' => 'يجب أن يحتوي حقل :attribute على :digits أرقام.',
    'digits_between' => 'يجب أن يحتوي حقل :attribute على أرقام تتراوح بين :min و :max.',
    'dimensions' => 'يحتوي حقل :attribute على أبعاد صورة غير صالحة.',
    'distinct' => 'يحتوي حقل :attribute على قيمة مكررة.',
    'email' => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صحيحًا.',
    'ends_with' => 'يجب أن ينتهي حقل :attribute بأحد القيم التالية: :values.',
    'enum' => 'قيمة حقل :attribute المحددة غير صالحة.',
    'exists' => 'قيمة حقل :attribute المحددة غير صالحة.',
    'file' => 'يجب أن يكون حقل :attribute ملفًا.',
    'filled' => 'يجب أن يحتوي حقل :attribute على قيمة.',
    'gt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول النص :attribute أكثر من :value حرفًا/أحرف.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل على :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أكبر من أو يساوي :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute أكبر من أو تساوي :value.',
        'string' => 'يجب أن يكون طول النص :attribute أكبر من أو يساوي :value حرفًا/أحرف.',
    ],
    'image' => 'يجب أن يكون حقل :attribute صورة.',
    'in' => 'قيمة حقل :attribute المحددة غير صالحة.',
    'in_array' => 'حقل :attribute غير موجود في :other.',
    'integer' => 'يجب أن يكون حقل :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن يكون حقل :attribute عنوان IP صحيحًا.',
    'ipv4' => 'يجب أن يكون حقل :attribute عنوان IPv4 صحيحًا.',
    'ipv6' => 'يجب أن يكون حقل :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن يكون حقل :attribute نصًا صحيحًا من نوع JSON.',
    'lt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute أقل من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute أقل من :value.',
        'string' => 'يجب أن يكون طول النص :attribute أقل من :value حرفًا/أحرف.',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :value عناصر/عنصر.',
        'file' => 'يجب ألا يتجاوز حجم الملف :attribute :value كيلوبايت.',
        'numeric' => 'يجب ألا تتجاوز قيمة :attribute :value.',
        'string' => 'يجب ألا يتجاوز طول النص :attribute :value حرفًا/أحرف.',
    ],
    'mac_address' => 'يجب أن يكون حقل :attribute عنوان MAC صحيحًا.',
    'max' => [
        'array' => 'يجب ألا يحتوي حقل :attribute على أكثر من :max عناصر/عنصر.',
        'file' => 'يجب ألا يتجاوز حجم الملف :attribute :max كيلوبايت.',
        'numeric' => 'يجب ألا تتجاوز قيمة :attribute :max.',
        'string' => 'يجب ألا يتجاوز طول النص :attribute :max حرفًا/أحرف.',
    ],
    'mimes' => 'يجب أن يكون حقل :attribute ملفًا من نوع: :values.',
    'mimetypes' => 'يجب أن يكون حقل :attribute ملفًا من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل على :min عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute على الأقل :min.',
        'string' => 'يجب أن يكون طول النص :attribute على الأقل :min حرفًا/أحرف.',
    ],
    'multiple_of' => 'يجب أن يكون حقل :attribute من مضاعفات :value.',
    'not_in' => 'قيمة حقل :attribute المحددة غير صالحة.',
    'not_regex' => 'صيغة حقل :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون حقل :attribute رقمًا.',
    'password' => [
        'letters' => 'يجب أن يحتوي حقل :attribute على حرف واحد على الأقل.',
        'mixed' => 'يجب أن يحتوي حقل :attribute على حرف كبير واحد وحرف صغير واحد على الأقل.',
        'numbers' => 'يجب أن يحتوي حقل :attribute على رقم واحد على الأقل.',
        'symbols' => 'يجب أن يحتوي حقل :attribute على رمز واحد على الأقل.',
        'uncompromised' => 'لقد ظهر :attribute المُدخل في تسريب للبيانات. يرجى اختيار :attribute مختلف.',
    ],
    'present' => 'يجب توفير حقل :attribute.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما يكون :other هو :value.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم يكن :other أحد :values.',
    'prohibits' => 'حقل :attribute يمنع تواجد :other.',
    'regex' => 'صيغة حقل :attribute غير صالحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'يجب أن يحتوي حقل :attribute على مُدخلات لكل من: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما يكون :other هو :value.',
    'required_unless' => 'حقل :attribute مطلوب ما لم يكن :other أحد :values.',
    'required_with' => 'حقل :attribute مطلوب عند تواجد :values.',
    'required_with_all' => 'حقل :attribute مطلوب عند تواجد جميع :values.',
    'required_without' => 'حقل :attribute مطلوب عند عدم تواجد :values.',
    'required_without_all' => 'حقل :attribute مطلوب عند عدم تواجد أي من :values.',
    'same' => 'يجب أن يتطابق حقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :size عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم الملف :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة :attribute :size.',
        'string' => 'يجب أن يكون طول النص :attribute :size حرفًا/أحرف.',
    ],
    'starts_with' => 'يجب أن يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'لا يجوز أن يبدأ حقل :attribute بأحد القيم التالية: :values.',
    'string' => 'يجب أن يكون حقل :attribute نصًا.',
    'timezone' => 'يجب أن يكون حقل :attribute منطقة زمنية صالحة.',
    'unique' => 'قيمة حقل :attribute مُستخدمة من قبل.',
    'uploaded' => 'فشل في تحميل حقل :attribute.',
    'url' => 'يجب أن يكون حقل :attribute رابطًا صحيحًا.',
    'uuid' => 'يجب أن يكون حقل :attribute معرف UUID صحيحًا.',

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
        'اسم-الحقل' => [
            'اسم-القاعدة' => 'رسالة-مخصصة',
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

    'attributes' => [],

];
