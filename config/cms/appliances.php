<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 11/17/17
 * Time: 6:33 PM
 */

return [
    //setting appliance
    [
        "show_in_toolbar" => true,
        "properties" => [
            "id" => "system_settings",
            "name" => "general.appliances.setting",
            "icon" => "/admin_dashboard/images/icons/settings.png",
            "route" => "admin.setting.appliances",
        ],
        "setting_appliances" => [
            [
                "properties" => [
                    "id" => "system_update",
                    "name" => "general.setting.system_update",
                    "icon" => "icon-update",
                    "route" => "admin.setting.upgrade.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "env_file",
                    "name" => "general.setting.env_file",
                    "icon" => "icon-env-file",
                    "route" => "admin.setting.env-file.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "setting_management",
                    "name" => "general.setting.setting_management",
                    "icon" => "icon-setting",
                    "route" => "admin.setting.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "action_log_setting",
                    "name" => "general.setting.action_log_setting",
                    "icon" => "icon-log",
                    "route" => "admin.setting.action-log-setting.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_modals",
                    "name" => "general.shop.modals",
                    "icon" => "icon-modal",
                    "route" => "admin.modal.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "language",
                    "name" => "general.setting.language",
                    "icon" => "icon-language",
                    "route" => "admin.setting.language.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "export-database",
                    "name" => "general.setting.export_database",
                    "icon" => "icon-export-database",
                    "route" => "admin.setting.database.export"
                ]
            ],
            [
                "properties" => [
                    "id" => "product-watermark",
                    "name" => "general.setting.product_watermark",
                    "icon" => "icon-product-watermark",
                    "route" => "admin.setting.product-watermark.edit"
                ]
            ]
        ],
        "explore_appliances" => []
    ],
    //user management appliance
    [
        "show_in_toolbar" => false,
        "properties" => [
            "id" => "user_management",
            "name" => "general.appliances.user",
        ],
        "setting_appliances" => [
            [
                "properties" => [
                    "id" => "user_management",
                    "name" => "general.setting.user_management",
                    "icon" => "icon-user",
                    "route" => "admin.user.index"
                ]
            ], [
                "properties" => [
                    "id" => "system_role_management",
                    "name" => "general.setting.system_role_management",
                    "icon" => "icon-role",
                    "route" => "admin.system-role.index"
                ]
            ]
        ],
        "explore_appliances" => []
    ],
    //shop appliance
    [
        "show_in_toolbar" => true,
        "properties" => [
            "id" => "shop",
            "name" => "general.appliances.shop",
            "icon" => "/admin_dashboard/images/icons/shop.png",
            "route" => "admin.shop.appliances",
        ],
        "setting_appliances" => [
            [
                "properties" => [
                    "id" => "state_management",
                    "name" => "general.setting.state_management",
                    "icon" => "icon-state",
                    "route" => "admin.state.index"
                ]
            ]
        ],
        "explore_appliances" => [
            [
                "properties" => [
                    "id" => "product_management",
                    "name" => "general.explore.product_management",
                    "icon" => "icon-product",
                    "route" => "admin.product.create"
                ]
            ]
        ],
        "shop_appliances" => [
            [
                "properties" => [
                    "id" => "shop_products",
                    "name" => "general.shop.products",
                    "icon" => "icon-product",
                    "route" => "admin.product.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "p_structure_management",
                    "name" => "general.setting.p_structure_management",
                    "icon" => "icon-p-structure",
                    "route" => "admin.p-structure.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "color_management",
                    "name" => "general.setting.color_management",
                    "icon" => "icon-color",
                    "route" => "admin.color.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_need_lists",
                    "name" => "general.shop.need-lists",
                    "icon" => "icon-need-list",
                    "route" => "admin.need-list.show-products"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_discount_group",
                    "name" => "general.shop.discount-groups",
                    "icon" => "icon-discount-group",
                    "route" => "admin.discount-group.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_customers",
                    "name" => "general.shop.customers",
                    "icon" => "icon-customer",
                    "route" => "admin.customer-user.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "representative",
                    "name" => "general.setting.representative",
                    "icon" => "icon-representative",
                    "route" => "admin.setting.representative.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "customer_meta_category_management",
                    "name" => "general.setting.customer_meta_category_management",
                    "icon" => "icon-customer-meta-category",
                    "route" => "admin.customer-meta-category.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_invoices",
                    "name" => "general.shop.invoices",
                    "icon" => "icon-invoice",
                    "route" => "admin.invoice.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_carts",
                    "name" => "general.shop.carts",
                    "icon" => "icon-customer-cart",
                    "route" => "admin.cart.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "cart_notification_management",
                    "name" => "general.setting.cart_notification_management",
                    "icon" => "icon-cart-notification",
                    "route" => "admin.setting.cart-notification.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "shipment_cost_management",
                    "name" => "general.setting.shipment_cost_management",
                    "icon" => "icon-shipment-cost",
                    "route" => "admin.setting.shipment-cost.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "logistic_management",
                    "name" => "general.setting.logistic_management",
                    "icon" => "icon-logistics",
                    "route" => "admin.setting.logistic.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "product_query_management",
                    "name" => "general.setting.product_query_management",
                    "icon" => "icon-product-query",
                    "route" => "admin.product-query.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "product_filter_management",
                    "name" => "general.setting.product_filter_management",
                    "icon" => "icon-product-filter",
                    "route" => "admin.product-filter.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "survey_management",
                    "name" => "general.setting.survey_management",
                    "icon" => "icon-survey",
                    "route" => "admin.setting.survey.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "shop_badges",
                    "name" => "general.shop.badges",
                    "icon" => "icon-badge",
                    "route" => "admin.badge.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "financial_driver",
                    "name" => "general.setting.financial_driver",
                    "icon" => "icon-financial-driver",
                    "route" => "admin.setting.financial-driver.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "sms_driver",
                    "name" => "general.setting.sms_driver",
                    "icon" => "icon-sms-driver",
                    "route" => "admin.setting.sms-driver.edit"
                ]
            ],
            [
                "properties" => [
                    "id" => "payment_driver",
                    "name" => "general.setting.payment_driver",
                    "icon" => "icon-payment-driver",
                    "route" => "admin.setting.payment-driver.edit"
                ]
            ],
        ]
    ],
    //web page management
    [
        "show_in_toolbar" => false,
        "properties" => [
            "id" => "web_management",
            "name" => "general.appliances.web_page",
        ],
        "setting_appliances" => [
            [
                "properties" => [
                    "id" => "web_page_management",
                    "name" => "general.setting.web_page_management",
                    "icon" => "icon-web-page",
                    "route" => "admin.web-page.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "gallery_management",
                    "name" => "general.setting.gallery_management",
                    "icon" => "icon-gallery",
                    "route" => "admin.gallery.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "web_form_management",
                    "name" => "general.setting.web_form_management",
                    "icon" => "icon-web-form",
                    "route" => "admin.web-form.index"
                ]
            ]
        ],
        "explore_appliances" => [
            [
                "properties" => [
                    "id" => "article_management",
                    "name" => "general.explore.article_management",
                    "icon" => "icon-web-page",
                    "route" => "admin.article.create"
                ]
            ]
        ]
    ],
    //file management
    [
        "show_in_toolbar" => true,
        "properties" => [
            "id" => "file_management",
            "name" => "general.appliances.directory",
            "icon" => "/admin_dashboard/images/icons/explore.png",
            "route" => "admin.directory.index",
        ],
        "setting_appliances" => [],
        "explore_appliances" => [
            [
                "properties" => [
                    "id" => "directory_management",
                    "name" => "general.explore.directory_management",
                    "icon" => "icon-folder",
                    "route" => "admin.directory.create"
                ]
            ]
        ]
    ],
    //tag management
    [
        "show_in_toolbar" => false,
        "properties" => [
            "id" => "tag_management",
            "name" => "general.appliances.tag",
        ],
        "setting_appliances" => [
            [
                "properties" => [
                    "id" => "tag_management",
                    "name" => "general.setting.tag_management",
                    "icon" => "icon-tag",
                    "route" => "admin.tag.index"
                ]
            ]
        ],
        "explore_appliances" => []
    ],
    //analytic appliance
    [
        "show_in_toolbar" => true,
        "properties" => [
            "id" => "analytic",
            "name" => "general.appliances.analytic",
            "icon" => "/admin_dashboard/images/icons/analytic.png",
            "route" => "admin.analytic.appliances",
        ],
        "setting_appliances" => [
            [
                "properties" => [
                    "id" => "edit_robots_txt",
                    "name" => "general.analytic.edit_robots_txt",
                    "icon" => "icon-edit-robot",
                    "route" => "admin.robot-txt.index"
                ]

            ],
        ],
        "explore_appliances" => [],
        'analytic_appliances' => [
            [
                "properties" => [
                    "id" => "live_reports",
                    "name" => "general.analytic.live_reports",
                    "icon" => "icon-live-reports",
                    "route" => "admin.live-reports.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "review_management",
                    "name" => "general.analytic.review_management",
                    "icon" => "icon-review",
                    "route" => "admin.review.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "rate_comment",
                    "name" => "general.analytic.rate_comment",
                    "icon" => "icon-rate-comment",
                    "route" => "admin.rate.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "web_form_messages",
                    "name" => "general.analytic.web_form_message",
                    "icon" => "icon-rate-message",
                    "route" => "admin.web-form-message.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "edit_robots_txt",
                    "name" => "general.analytic.edit_robots_txt",
                    "icon" => "icon-edit-robot",
                    "route" => "admin.robot-txt.index"
                ]

            ],
            [
                "properties" => [
                    "id" => "action_logs",
                    "name" => "general.analytic.action_logs",
                    "icon" => "icon-log",
                    "route" => "admin.action-log.index"
                ]
            ],
            [
                "properties" => [
                    "id" => "debug_logs",
                    "name" => "general.analytic.debug_logs",
                    "icon" => "icon-debug-logs",
                    "route" => "admin.debug-log.index"
                ]
            ],
        ]
    ],
    [
        "show_in_toolbar" => true,
        "properties" => [
            "id" => "short_links",
            "name" => "general.appliances.short_links",
            "icon" => "/admin_dashboard/images/icons/link-shortener.png",
            "route" => "admin.short-link.index"
        ],
    ],

];
