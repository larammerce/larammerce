<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/5/17
 * Time: 6:33 PM
 */

return [
    "directory" => [
        "role_attached" => "The role was successfully added to the folder ",
        "role_detached" => "The role was successfully removed from the folder",
        "badge_attached" => "Badge successfully added to this folder",
        "badge_detached" => "The badge has been successfully removed from this folder",
        "badge_attached_to_products" => "The badge was successfully added to this folder and all its products",
        "badge_detached_from_products" => "The badge has been successfully removed from this folder and all its products",
        "badge_attached_to_articles" => "The badge was successfully added to this folder and all its blogs",
        "badge_detached_from_articles" => "The badge was successfully removed from this folder and all its blogs",
    ],
    "customer_user" => [
        "activation_failed" => "Unfortunately, it is not possible to activate the desired customer in the financial system. Please contact system support",
        "no_national_code" => "Please add the national code first to complete your order."
    ],
    "article" => [
        "tag_attached" => "The tag has been successfully added to the blog",
        "tag_detached" => "The tag has been successfully removed from the blog",
    ],
    "product" => [
        "attribute_attached" => "The feature has been successfully added to the product",
        "attribute_detached" => "The feature has been successfully removed from the product",
        "color_attached" => "Color has been successfully added to the item",
        "color_detached" => "The color has been successfully removed from the item",
        "tag_attached" => "The tag has been successfully added to the product",
        "tag_detached" => "The tag has been successfully removed from the product",
        "badge_attached" => "The badge has been successfully added to the product",
        "badge_detached" => "The badge has been successfully removed from the item",
    ],
    "p_structure" => [
        "attribute_key_attached" => "The key has been successfully added to the template",
        "attribute_key_detached" => "The badge has been successfully removed from the item",
        "attribute_key_made_sortable" => "The key was successfully sorted.",
        "attribute_key_made_not_sortable" => "The key was successfully unsorted.",
    ],
    "system_user" => [
        "role_attached" => "The role has been successfully added to the system user",
        "role_detached" => "The role has been successfully removed from the system user",
    ],
    "web_page" => [
        "tag_attached" => "The tag has been successfully added to the webpage",
        "tag_detached" => "The tag has been successfully removed from the webpage",
    ],
    "product_image" => [
        "image_uploaded" => "Photo uploaded successfully",
        "image_not_uploaded" => "Unfortunately, there was a problem uploading the photo",
        "image_edited" => "Photo edited successfully",
        "image_deleted" => "Photo deleted successfully",
        "main_image_changed" => "The original photo was replaced",
        "secondary_image_changed" => "The secondary photo was replaced",
    ],
    "product_cart" => [
        "product_attached" => "The product has been successfully added to the cart",
        "product_not_attached" => [
            "unknown" => "There was a problem adding the product to the cart",
            "due_to_duplicate" => "The desired product is available in the cart",
            "due_to_invalid_product" => "It is not possible to add the product to the basket",
            "due_to_count_limit" => "Due to the limited number of items in the basket, it is not possible to add a new product",
        ],
        "product_detached" => "The product has been successfully removed from the shopping cart",
        "product_not_detached" => "There was a problem removing the product from the cart",
    ],
    "address" => [
        "created" => "Your address has been saved successfully.",
        "invalid" => "The address you want is not available in the system.",
        "updated" => "Your address has been successfully updated.",
        "deleted" => "Your desired address has been successfully deleted.",
        "set_as_main" => "Original address changed."
    ],

    "excel" => [
        "row_not_valid" => "The :row line of the Excel file is not valid.",
        "importable_attributes_not_set" => "Output fields are not defined",
        "row_validation_failed" => "Unfortunately, the information in the :row line is not valid. :message"
    ],

    "debug_log" => [
        "unknown_debug_log_type_error" => "The desired log type is not valid",
    ],

    "discount_group" => [
        "soft_delete_success" => "The discount group was successfully deleted.",
        "soft_delete_fail" => "Unfortunately, it is not possible to delete the desired discount group. Please contact the system support.",
        "restore_success" => "The discount group was successfully restored.",
        "restore_fail" => "Unfortunately, it is not possible to restore the desired discount group. Please contact the system support."
    ],

    "product_watermark" => [
        "update" => [
            "success" => "Watermark settings have been successfully updated.",
            "failed" => "Unfortunately, the watermark settings were not updated."
        ],
        "remove_image" => [
            "success" => "Image deleted successfully.",
            "failed" => "Unfortunately, the image was not deleted."
        ],
        "process" => [
            "success" => "Image processed successfully.",
            "failed" => "Unfortunately, the image could not be processed."
        ],
    ],

    "languages" => [
        "language_added_successfully" => "The desired language has been successfully added.",
    ],
];
