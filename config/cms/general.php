<?php

return [
    "site" => [
        "model_options" => env('SITE_MODEL_OPTIONS', false),
        "only_main_models" => env('SITE_ONLY_MAIN_MODELS_VIEW', true),
        "show_deactivated_products" => env('SITE_SHOW_DEACTIVATED_PRODUCTS', false),
        "enable_directory_location" => env('SITE_ENABLE_DIRECTORY_LOCATION', false),
        "product_sort" => env("SITE_DEFAULT_PRODUCT_SORT", "created_at:desc"),
        "stock_manager_notification" => env("SITE_STOCK_MANAGER_NOTIFICATION", true)
    ]
];
