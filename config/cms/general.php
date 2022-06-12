<?php

return [
    "site" => [
        "model_options" => env('SITE_MODEL_OPTIONS', false),
        "show_deactivated_products" => env('SITE_SHOW_DEACTIVATED_PRODUCTS', false),
        "enable_directory_location" => env('SITE_ENABLE_DIRECTORY_LOCATION', false),
        "product_sort" => env("SITE_DEFAULT_PRODUCT_SORT", "created_at:desc")
    ]
];
