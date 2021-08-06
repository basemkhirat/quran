<?php

return array(
    /*
      |--------------------------------------------------------------------------
      | Validation Language Lines
      |
      | اسطر التحقق والتصديق للغة العربية
      |--------------------------------------------------------------------------
      |
      | The following language lines contain the default error messages used by
      | the validator class
      |
      | الاسطر ادناه تحتوي علي رسائل الخطأ الافتراضية المستخدمة في فئة التحقق
      |
      | Some of these rules have multiple versions such as the size rules
      |
      | بعض هذه القواعد تحتوي علي عدة نسخ مثل قاعدة الحجم
      |
      | Feel free to tweak each of these messages
      |
      | لا تتردد في تعديل اي منها
      |
     */

    "accepted" => "قيمة :attribute يجب أن يتم قبول",
    "active_url" => "قيمة :attribute ليس عنوان إنترنت صالحةًا",
    "before" => "قيمة :attribute يجب أن يكون تاريخ قبل :date",
    "after" => "قيمة :attribute يجب أن يكون تاريخًا بعد :date",
    "alpha" => "قيمة :attribute يجب أن يحتوي  على أحرفاً فقط",
    "alpha_dash" => "قيمة :attribute يجب أن يحتوي  على أحرف وأرقام وإشارة ناقص",
    "alpha_num" => "قيمة :attribute يجب أن يحتوي  على أحرف وأرقام",
    "array" => "قيمة :attribute يجب ان تكون مصفوفة",
    "between" => array(
        "numeric" => "قيمة :attribute يجب أن يكون رقم بين :min - :max",
        "file" => "قيمة :attribute يجب أن يكون بين :min - :max كيلو بايت",
        "string" => "قيمة :attribute يجب أن يكون طوله بين :min - :max من الأحرف",
        "array" => "قيمة :attribute يجب ان يحتوي علي :min - :max بنود"
    ),
    "boolean" => "The قيمة :attribute field must be true or false",
    "confirmed" => "تأكيد قيمة :attribute لا يتطابق",
    "date" => "قيمة :attribute ليس تاريخ صحيح",
    "date_format" => "قيمة :attribute لا يطابق اليصغة :format",
    "different" => "قيمة :attribute و :other يجب أن يكونا مختلفين",
    "digits" => "قيمة :attribute يجب أن يتكون من :digits أرقام",
    "digits_between" => "قيمة :attribute يجب أن يكون بين :min و :max أرقام",
    "email" => "قيمة :attribute بصيغة خاطئة",
    "exists" => "قيمة :attribute المختار غير صالحة",
    "image" => "قيمة :attribute يجب أن يكون صورة",
    "in" => "قيمة :attribute غير صالحة",
    "integer" => "قيمة :attribute يجب أن يكون رقماً صحيحاً",
    "ip" => "قيمة :attribute يجب أن يكون عنوان أنترنت (IP) صحيحاً",
    "max" => array(
        "numeric" => "قيمة :attribute يجب ألا يكون أكبر من :max",
        "file" => "قيمة :attribute يجب ألا يكون أكبر من :max كيلو بايت",
        "string" => "قيمة :attribute يجب ألا يكون أكبر من :max حرف",
        "array" => "قيمة :attribute يجب ان لا يزيد علي :max بنود"
    ),
    "mimes" => "قيمة :attribute يجب أن يكون ملف من نوع: :values",
    "min" => array(
        "numeric" => "قيمة :attribute يجب أن يكون على الأقل :min",
        "file" => "قيمة :attribute يجب أن يكون على الأقل :min كيلو بايت",
        "string" => "قيمة :attribute يجب أن يكون طوله على الأقل :min أحرف",
        "array" => "قيمة :attribute يجب ان يحتوي علي الاقل :min بنود"
    ),
    "not_in" => "قيمة :attribute المختار غير صالحة",
    "numeric" => "قيمة :attribute يجب أن يكون رقم",
    "regex" => "قيمة :attribute صيغته غير صالحة",
    "required" => "قيمة :attribute مطلوبة",
    "required_if" => "قيمة :attribute مطلوب عندما :other يساوي :value",
    "required_with" => "قيمة :attribute مطلوب عندما يكون :values موجوداً",
    "required_with_all" => "قيمة :attribute مطلوب عندما يكون :values is موجوداً",
    "required_without" => "قيمة :attribute مطلوب عندما لا يكون :values موجوداً",
    "required_without_all" => " قيمة :attribute مطلوب عندما لا يكون :values موجوداً",
    "same" => "قيمة :attribute و :other يجب أن يتطابقا",
    "size" => array(
        "numeric" => "قيمة :attribute يجب أن يكون :size",
        "file" => "قيمة :attribute يجب أن يكون :size كيلو بايت",
        "string" => "قيمة :attribute يجب أن يتكون من :size أحرف",
        "array" => "قيمة :attribute يجب ان يحتوي على :size بنود"
    ),
    "timezone" => "The قيمة :attribute must be a valid zone",
    "unique" => "قيمة :attribute مستخدم مسبقاً",
    "url" => "قيمة :attribute صيغته غير صحيحة",
    "file_extension" => "ktb لابد أن يكون الملف من نوع",
    /*
      |--------------------------------------------------------------------------
      | Custom Validation Language Lines
      |
      | اسطر التحقق المخصصه للغة العربية
      |
      |--------------------------------------------------------------------------
      |
      | Here you may specify custom validation messages for attributes using the
      | convention "attributerule" to name the lines
      |
      | من هنا يمكنك تحديد رسائل تحقق مخصصه للسمات باستخدام مجمع "attributerule"
      | لتسمية السطر
      |
      | his makes it quick to specify a specific custom language line for a given
      | attribute rule
      |
      | يكون التحديد سريعا عند استخدام سمه معينة للغة المخصصة
      |
     */
    'custom' => array(
        'page_title' => array(
            'required' => 'custom-message'
        ),
    ),
    /*
      |--------------------------------------------------------------------------
      | Custom Validation Attributes
      |
      | سمات التحقق المخصصه
      |
      |--------------------------------------------------------------------------
      |
      | The following language lines are used to swap attribute place-holders
      | with something more reader friendly such as E-Mail Address instead
      | of "email" This simply helps us make messages a little cleaner
      |
      | الاسطر ادناه تستخدم لتبديل السمات بشكل مقروء اكثر مثل "البريد الالكتروني"
      | بدلا عن "الايميل" هذه سيساعد في جعل الرسائل اوضح
      |
     */
    'attributes' => array(
        "page_title" => "عنوان الصفحة",
        "name" => "الإسم",
        "name.ar" => "الإسم بالعربية",
        "username" => "اسم المستخدم",
        "email" => "البريد الالكتروني",
        "first_name" => "الاسم الأول",
        "last_name" => "اسم العائلة",
        "password" => "كلمة السر",
        "city" => "المدينة",
        "country" => "الدولة",
        "address" => "العنوان",
        "phone" => "الهاتف",
        "mobile" => "الجوال",
        "age" => "العمر",
        "sex" => "الجنس",
        "gender" => "النوع",
        "day" => "اليوم",
        "month" => "الشهر",
        "year" => "السنة",
        "hour" => "ساعة",
        "minute" => "دقيقة",
        "second" => "ثانية",
        "title" => "العنوان",
        "content" => "المحتوى",
        "description" => "الوصف",
        "excerpt" => "الملخص",
        "date" => "التاريخ",
        "time" => "الوقت",
        "available" => "متاح",
        "size" => "الحجم",
        "lang" => "اللغة",
        "tashkeel" => "إظهار التشكيل",
        "mode" => "الوضع",
        "page" => "الصفحة",
        "code" => "الكود",
        "text" => 'النص',
        "comment" => "التعليق",
        "book_id" => "الكتاب",
        "page_id" => "الصفحة",
        "type" => "النوع",
        "file" => "الملف",
        "books" => "الكتب",
        "last_update" => "آخر تحديث",
        "sura" => "السورة",
        "aya" => "الآية",
        "books" => "الكتب",
        "platform" => "المنصة",
        "version" => "الإصدار",
        "subject" => "الموضوع",
        "message" => "الرسالة",
        "category_id" => "التصنيف",
        "name_ar" => "الإسم بالعربية",
        "content_ar" => "الإجابة بالعربية",
        "stars" => "النجوم",
        "linguist_stars" => "التقييم اللغوي",
        "wording_stars" => "تقييم الصياغة والأسلوب",
        "legal_stars" => "التقييم الشرعي"
    ),
);
