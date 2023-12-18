<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Locales
    |--------------------------------------------------------------------------
    |
    | Contains an array with the applications available locales.
    |
    */
    "locales" => explode(",", env("SITE_ENABLED_LOCALES", "fa")),

    /*
    |--------------------------------------------------------------------------
    | Locale separator
    |--------------------------------------------------------------------------
    |
    | This is a string used to glue the language and the country when defining
    | the available locales. Example: if set to "-", then the locale for
    | colombian spanish will be saved as "es-CO" into the database.
    |
    */
    "locale_separator" => "-",

    /*
    |--------------------------------------------------------------------------
    | Default locale
    |--------------------------------------------------------------------------
    |
    | As a default locale, Translatable takes the locale of Laravel"s
    | translator. If for some reason you want to override this,
    | you can specify what default should be used here.
    | If you set a value here it will only use the current config value
    | and never fallback to the translator one.
    |
    */
    "locale" => env('SITE_DEFAULT_LOCALE', 'fa'),

    /*
    |--------------------------------------------------------------------------
    | Use fallback
    |--------------------------------------------------------------------------
    |
    | Determine if fallback locales are returned by default or not. To add
    | more flexibility and configure this option per "translatable"
    | instance, this value will be overridden by the property
    | $useTranslationFallback when defined
    |
    */
    "use_fallback" => false,

    /*
    |--------------------------------------------------------------------------
    | Use fallback per property
    |--------------------------------------------------------------------------
    |
    | The property fallback feature will return the translated value of
    | the fallback locale if the property is empty for the selected
    | locale. Note that "use_fallback" must be enabled.
    |
     */
    "use_property_fallback" => true,

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | A fallback locale is the locale being used to return a translation
    | when the requested translation is not existing. To disable it
    | set it to false.
    | If set to null it will loop through all configured locales until
    | one existing is found or end of list reached. The locales are looped
    | from top to bottom and for country based locales the simple one
    | is used first. So "es" will be checked before "es_MX".
    |
    */
    "fallback_locale" => env("SITE_DEFAULT_LOCALE", "fa"),

    /*
    |--------------------------------------------------------------------------
    | All Locales
    |--------------------------------------------------------------------------
    |
    | All available locales that can be selected.
    |
    */
    "available_locales" => explode(',', env('SITE_AVAILABLE_LOCALES','fa')),

    /*
    |--------------------------------------------------------------------------
    | All locales default data
    |--------------------------------------------------------------------------
    |
    | All available locales that can be selected.
    |
    */
    'all_locales_data' => [
        'ar' => ['name' => 'العربیه',],
        'fa' => ['name' => 'فارسی',],
        'en' => ['name' => 'English',],
        'tr' => ['name' => 'Türkçe',],
        'tr' => ['name' => 'Deutsch',],
        'tr' => ['name' => 'Pусский',],
        'tr' => ['name' => 'Italiano',],
        'tr' => ['name' => 'Le français',],
        'tr' => ['name' => 'Español',],
    ],


    /*
    |--------------------------------------------------------------------------
    | RTL Locales
    |--------------------------------------------------------------------------
    |
    | The rtl locales that change the direction of website design.
    |
    */
    "rtl_locales" => ["fa", "ar"],

    /*
    |--------------------------------------------------------------------------
    | Translation Model Namespace
    |--------------------------------------------------------------------------
    |
    | Defines the default "Translation" class namespace. For example, if
    | you want to use App\Translations\CountryTranslation instead of App\CountryTranslation
    | set this to "App\Translations".
    |
    */
    "translation_models_namespace" => "App\\TranslationModels",
    "models_namespace" => "App\\Models",
    "migrations_path" => "database/migrations/translations",

    /*
    |--------------------------------------------------------------------------
    | Translation Trait Namespace
    |--------------------------------------------------------------------------
    |
    | Defines the translatable trait path which used for check if a model use the trait.
    |
    */
    "translation_trait" => "App\\Utils\\Translation\\Traits\\Translatable",

    /*
    |--------------------------------------------------------------------------
    | Translation Suffix
    |--------------------------------------------------------------------------
    |
    | Defines the default "Translation" class suffix. For example, if
    | you want to use CountryTrans instead of CountryTranslation
    | application, set this to "Trans".
    |
    */
    "translation_suffix" => "T",

    /*
    |--------------------------------------------------------------------------
    | Locale key
    |--------------------------------------------------------------------------
    |
    | Defines the "locale" field name, which is used by the
    | translation model.
    |
    */
    "locale_key" => "locale",

    /*
    |--------------------------------------------------------------------------
    | Always load translations when converting to array
    |--------------------------------------------------------------------------
    | Language this to false will have a performance improvement but will
    | not return the translations when using toArray(), unless the
    | translations relationship is already loaded.
    |
     */
    "to_array_always_loads_translations" => true,

    /*
    |--------------------------------------------------------------------------
    | Configure the default behavior of the rule factory
    |--------------------------------------------------------------------------
    | The default values used to control the behavior of the RuleFactory.
    | Here you can set your own default format and delimiters for
    | your whole app.
     *
     */
    "rule_factory" => [
        "format" => \App\Utils\Translation\Validation\RuleFactory::FORMAT_ARRAY,
        "prefix" => "%",
        "suffix" => "%",
    ],
];
