<?php

namespace App\Validations;

class ProductValidation {
    const EXCEL_ROW = [
        "id" => "nullable|exists:products,id",
        "title" => "string|max:255",
        "latest_price" => "nullable|int",
        "latest_special_price" => "nullable|int",
        "directory_id" => "exists:directories,id",
        "p_structure_id" => "exists:p_structures,id",
        "description" => "nullable|string",
        "code" => "required",
        "average_rating" => "nullable|float",
        "rates_count" => "nullable|int",
        "is_active" => "nullable|bool",
        "min_allowed_count" => "nullable|int",
        "max_purchase_count" => "nullable|int",
        "min_purchase_count" => "nullable|int",
        "is_important" => "nullable|bool",
        "seo_title" => "nullable|string",
        "seo_keywords" => "nullable|string",
        "seo_description" => "nullable|string",
        "model_id" => "nullable|exists:products,id",
        "has_discount" => "nullable|bool",
        "previous_price" => "nullable|nullable|int",
        "is_accessory" => "nullable|bool",
        "is_visible" => "nullable|bool",
        "inaccessibility_type" => "nullable|int",
        "cmc_id" => "nullable|exists:customer_meta_categories,id",
        "notice" => "nullable|string",
        "discount_group_id" => "nullable|exists:discount_groups,id",
        "priority" => "nullable|int",
        "is_discountable" => "nullable|bool",
        "structure_sort_score" => "nullable|int",
        "is_package" => "nullable|bool",
        "accessory_for" => "nullable|exists:products,id"
    ];
}
