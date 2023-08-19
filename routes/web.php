<?php

use App\Http\Controllers\Admin\PStructureController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

//Admin private routes
Route::group(
    [
        "middleware" => ["admin", "admin-request"],
        "namespace" => "Admin",
        "prefix" => "admin"
    ],
    function () {
        Route::get("/", "AdminController@index");

        //Setting
        Route::group(["prefix" => "setting", "as" => "admin.setting."],
            function () {
                Route::get("appliances", ["as" => "appliances", "uses" => "AdminController@settingAppliances"]);

                Route::get("survey", ["as" => "survey.edit", "uses" => "SurveyController@edit"]);
                Route::put("survey", ["as" => "survey.update", "uses" => "SurveyController@update"]);

                Route::get("logistic", ["as" => "logistic.edit", "uses" => "LogisticController@edit"]);
                Route::put("logistic", ["as" => "logistic.update", "uses" => "LogisticController@update"]);
                Route::post("logistic-add-column", ["as" => "logistic.add-column", "uses" => "LogisticController@addColumn"]);
                Route::get("logistic/get-invoices", ["as" => "logistic.get-invoices", "uses" => "LogisticController@getInvoices"]);
                Route::get("ajax-get-logistic-cells", ["as" => "logistic.ajax-get-cells", "uses" => "LogisticController@ajaxUpdateCells"]);

                Route::get("shipment-cost", ["as" => "shipment-cost.edit", "uses" => "ShipmentCostController@edit"]);
                Route::put("shipment-cost", ["as" => "shipment-cost.update", "uses" => "ShipmentCostController@update"]);

                Route::get("cart-notification", ["as" => "cart-notification.edit", "uses" => "CartNotificationController@edit"]);
                Route::put("cart-notification", ["as" => "cart-notification.update", "uses" => "CartNotificationController@update"]);

                Route::get("action-log-setting", ["as" => "action-log-setting.edit", "uses" => "ActionLogSettingController@edit"]);
                Route::put("action-log-setting", ["as" => "action-log-setting.update", "uses" => "ActionLogSettingController@update"]);

                Route::get("payment-driver", ["as" => "payment-driver.edit", "uses" => "PaymentDriverController@edit"]);
                Route::put("payment-driver", ["as" => "payment-driver.update", "uses" => "PaymentDriverController@update"]);
                Route::any("payment-driver/{driver_id}/remove-file/", ["as" => "payment-driver.remove-file",
                    "uses" => "PaymentDriverController@removeFile"]);

                Route::get("financial-driver", ["as" => "financial-driver.edit", "uses" => "FinancialDriverController@edit"]);
                Route::put("financial-driver", ["as" => "financial-driver.update", "uses" => "FinancialDriverController@update"]);

                Route::get("sms-driver", ["as" => "sms-driver.edit", "uses" => "SMSDriverController@edit"]);
                Route::put("sms-driver", ["as" => "sms-driver.update", "uses" => "SMSDriverController@update"]);

                Route::get("language", ["as" => "language.edit", "uses" => "LanguageSettingController@edit"]);
                Route::put("language", ["as" => "language.update", "uses" => "LanguageSettingController@update"]);

                Route::get("representative", ["as" => "representative.edit", "uses" => "RepresentativeSettingController@edit"]);
                Route::put("representative", ["as" => "representative.update", "uses" => "RepresentativeSettingController@update"]);

                Route::get("upgrade", ["as" => "upgrade.index", "uses" => "UpgradeController@index"]);
                Route::post("upgrade", ["as" => "upgrade.save-config", "uses" => "UpgradeController@saveConfig"]);

                Route::get("env-file", ["as" => "env-file.edit", "uses" => "EnvFileController@edit"]);
                Route::put("env-file", ["as" => "env-file.update", "uses" => "EnvFileController@update"]);

                Route::get("database/export", ["as" => "database.export", "uses" => "DatabaseController@export"]);
            });
        Route::resource("setting", "SettingController", ["as" => "admin"]);

        //Upgrade
        Route::any("upgrade", ["as" => "upgrade", "uses" => "UpgradeController@doUpgrade"]);

        //Shop
        Route::group(["prefix" => "shop", "as" => "admin.shop."],
            function () {
                Route::get("appliances", ["as" => "appliances", "uses" => "AdminController@shopAppliances"]);
            });

        //Analytic
        Route::group(["prefix" => "analytic", "as" => "admin.analytic."],
            function () {
                Route::get("appliances", ["as" => "appliances", "uses" => "AdminController@analyticAppliances"]);
            });

        //NeedList
        Route::group(["prefix" => "need-list", "as" => "admin.need-list."],
            function () {
                Route::get("/products", ["as" => "show-products", "uses" => "NeedListController@showProducts"]);
                Route::get("/product/{product}", ["as" => "show-product", "uses" => "NeedListController@showProduct"]);
                Route::get("/customer-user/{customer_user}", ["as" => "show-customer-user", "uses" => "NeedListController@showCustomerUser"]);
            });

        //User
        Route::resource("user", "UserController", ["as" => "admin"]);

        //SystemUser
        Route::group(["prefix" => "system-user", "as" => "admin.system-user."],
            function () {
                Route::post("{system_user}/attach-roles", ["as" => "attach-roles", "uses" => "SystemUserController@attachRoles"]);
                Route::post("{system_user}/attach-role", ["as" => "attach-role", "uses" => "SystemUserController@attachRole"]);
                Route::post("{system_user}/detach-role", ["as" => "detach-role", "uses" => "SystemUserController@detachRole"]);
                Route::any("{system_user}/remove-image", ["as" => "remove-image", "uses" => "SystemUserController@removeImage"]);
            });
        Route::resource("system-user", "SystemUserController", ["as" => "admin"]);

        //SystemRole
        Route::resource("system-role", "SystemRoleController", ["as" => "admin"]);

        //Invoice
        Route::group(["prefix" => "invoice", "as" => "admin.invoice."],
            function () {
                Route::post("{invoice}/attach-products", ["as" => "attach-products", "uses" => "InvoiceController@attachProducts"]);
                Route::get("{invoice}/shipment-sending", ["as" => "show-shipment-sending", "uses" => "InvoiceController@showShipmentSending"]);
                Route::post("{invoice}/shipment-sending", ["as" => "set-shipment-sending", "uses" => "InvoiceController@setShipmentSending"]);
                Route::get("{invoice}/shipment-delivered", ["as" => "show-shipment-delivered", "uses" => "InvoiceController@showShipmentDelivered"]);
                Route::post("{invoice}/shipment-delivered", ["as" => "set-shipment-delivered", "uses" => "InvoiceController@setShipmentDelivered"]);
                Route::get("{invoice}/shipment-exit-tab", ["as" => "show-shipment-exit-tab", "uses" => "InvoiceController@showShipmentExitTab"]);
                Route::post("{invoice}/shipment-exit-tab", ["as" => "set-shipment-exit-tab", "uses" => "InvoiceController@setShipmentExitTab"]);
            });
        Route::resource("invoice", "InvoiceController", ["as" => "admin"]);

        //InvoiceRow
        Route::group(["prefix" => "invoice-row", "as" => "admin.invoice-row."],
            function () {
            });
        Route::resource("invoice-row", "InvoiceRowController", ["as" => "admin"]);

        //Customer Cart
        Route::group(["prefix" => "cart", "as" => "admin.cart."],
            function () {
                Route::get("/", ["as" => "index", "uses" => "CartController@index"]);
                Route::get("{customer_user}", ["as" => "show", "uses" => "CartController@show"]);
                Route::put("{customer_user}/set-checked", ["as" => "set-checked", "uses" => "CartController@setChecked"]);
            });

        //CustomerUser
        Route::group(["prefix" => "customer-user", "as" => "admin.customer-user."],
            function () {
                Route::put("{customer_user}/activate", ["as" => "activate", "uses" => "CustomerUserController@activate"]);
            });
        Route::resource("customer-user", "CustomerUserController", ["as" => "admin"]);

        //DiscountGroup
        Route::group(["prefix" => "discount-group", "as" => "admin.discount-group."],
            function () {
                Route::get("{discount_group}/filter", ["as" => "product-filter.index", "uses" => "DiscountGroupController@indexProductFilter"]);
                Route::get("{discount_group}/filter/create", ["as" => "product-filter.create", "uses" => "DiscountGroupController@createProductFilter"]);
                Route::post("{discount_group}/filter", ["as" => "product-filter.attach", "uses" => "DiscountGroupController@attachProductFilter"]);
                Route::delete("{discount_group}/filter/{product_filter}", ["as" => "product-filter.detach", "uses" => "DiscountGroupController@detachProductFilter"]);
                Route::delete("{discount_group}/soft-delete", ["as" => "soft-delete", "uses" => "DiscountGroupController@softDelete"]);
                Route::patch("{discount_group_id}/restore", ["as" => "restore", "uses" => "DiscountGroupController@restore"]);
            }
        );
        Route::resource("discount-group", "DiscountGroupController", ["as" => "admin"]);
        
        //DiscountCard
        Route::group(["prefix" => "discount-card", "as" => "admin.discount-card."],
            function () {
                Route::post("notify/{discount_card}", ["as" => "notify", "uses" => "DiscountCardController@notify"]);
            });
        Route::resource("discount-card", "DiscountCardController", ["as" => "admin"]);

        //CustomerUserLegalInfo
        Route::resource("customer-user-legal-info", "CustomerUserLegalInfoController", ["as" => "admin"]);

        //CustomerAddress
        Route::resource("customer-address", "CustomerAddressController", ["as" => "admin"]);

        //CustomerMetaCategory
        Route::group(["prefix" => "customer-meta-category", "as" => "admin.customer-meta-category."],
            function () {
                Route::put("clone/{directory}", ["as" => "clone", "uses" => "CustomerMetaCategoryController@clone"]);
            });
        Route::resource("customer-meta-category", "CustomerMetaCategoryController", ["as" => "admin"]);

        //Rate
        Route::group(["prefix" => "rate", "as" => "admin.rate."],
            function () {
                Route::post("{rate}/change-accept-state", ["as" => "change-accept-state", "uses" => "RateController@changeAcceptRate"]);
            });
        Route::resource("rate", "RateController", ["as" => "admin"]);

        //WebForm
        Route::group(["prefix" => "web-form", "as" => "admin.web-form."],
            function () {
                Route::get("/", ["as" => "index", "uses" => "WebFormController@index"]);
                Route::get("show/{web_form}", ["as" => "show", "uses" => "WebFormController@show"]);
            });

        //WebFormMessage
        Route::group(["prefix" => "web-form-message", "as" => "admin.web-form-message."],
            function () {
                Route::get("/", ["as" => "index", "uses" => "WebFormMessageController@index"]);
                Route::get("{web_form_message}/show", ["as" => "show", "uses" => "WebFormMessageController@show"]);
            });

        //State
        Route::group(["prefix" => "state", "as" => "admin.state."],
            function () {
                Route::get("search", ["as" => "search", "uses" => "StateController@search"]);
            });
        Route::resource("state", "StateController", ["as" => "admin"]);

        //City
        Route::group(["prefix" => "city", "as" => "admin.city."],
            function () {
                Route::get("search", ["as" => "search", "uses" => "CityController@search"]);
            });
        Route::resource("city", "CityController", ["as" => "admin"]);

        //District
        Route::group(["prefix" => "district", "as" => "admin.district."],
            function () {
                Route::get("search", ["as" => "search", "uses" => "DistrictController@search"]);
            });
        Route::resource("district", "DistrictController", ["as" => "admin"]);

        //CustomerUser
        Route::resource("customer-user", "CustomerUserController", ["as" => "admin"]);

        //Directory
        Route::group(["prefix" => "directory", "as" => "admin.directory."],
            function () {
                Route::post("{directory}/attach-role", ["as" => "attach-role", "uses" => "DirectoryController@attachRole"]);
                Route::post("{directory}/detach-role", ["as" => "detach-role", "uses" => "DirectoryController@detachRole"]);
                Route::post("{directory}/attach-badge", ["as" => "attach-badge", "uses" => "DirectoryController@attachBadge"]);
                Route::post("{directory}/detach-badge", ["as" => "detach-badge", "uses" => "DirectoryController@detachBadge"]);
                Route::any("appliances", ["as" => "appliances", "uses" => "DirectoryController@appliances"]);
                Route::any("{directory}/remove-image", ["as" => "remove-image", "uses" => "DirectoryController@removeImage"]);
                Route::get("search", ["as" => "search", "uses" => "DirectoryController@search"]);
                Route::get("{directory}/link-product", ["as" => "show-link-product", "uses" => "DirectoryController@showLinkProduct"]);
                Route::post("{directory}/link-product", ["as" => "do-link-product", "uses" => "DirectoryController@doLinkProduct"]);
                Route::post("{directory}/{product}/unlink-product", ["as" => "unlink-product", "uses" => "DirectoryController@unlinkProduct"]);
                Route::get("{directory}/special-price", ["as" => "special-price.edit", "uses" => "DirectoryController@editSpecialPrice"]);
                Route::post("{directory}/special-price", ["as" => "special-price.update", "uses" => "DirectoryController@updateSpecialPrice"]);
                Route::delete("{directory}/special-price", ["as" => "special-price.destroy", "uses" => "DirectoryController@destroySpecialPrice"]);
                Route::get("{directory}/sync", ["as" => "sync", "uses" => "DirectoryController@sync"]);
                Route::get("cache-clear", ["as" => "cache-clear", "uses" => "DirectoryController@cacheClear"]);
            });
        Route::resource("directory", "DirectoryController", ["as" => "admin"]);

        //DirectoryLocation
        Route::group(["prefix" => "directory-location", "as" => "admin.directory-location."],
            function () {
            });
        Route::resource("directory-location", "DirectoryLocationController", ["as" => "admin"]);

        //Article
        Route::group(["prefix" => "article", "as" => "admin.article."],
            function () {
                Route::post("{article}/attach-tags", ["as" => "attach-tags", "uses" => "ArticleController@attachTags"]);
                Route::post("{article}/attach-tag", ["as" => "attach-tag", "uses" => "ArticleController@attachTag"]);
                Route::post("{article}/detach-tag", ["as" => "detach-tag", "uses" => "ArticleController@detachTag"]);
                Route::any("{article}/remove-image", ["as" => "remove-image", "uses" => "ArticleController@removeImage"]);
                Route::get("search", ["as" => "search", "uses" => "ArticleController@search"]);
            });
        Route::resource("article", "ArticleController", ["as" => "admin"]);

        //PStructureAttrKey
        Route::group(["prefix" => "p-structure-attr-key", "as" => "admin.p-structure-attr-key."],
            function () {
                Route::any("query", ["as" => "query", "uses" => "PStructureAttrKeyController@query"]);
            });
        Route::resource("p-structure-attr-key", "PStructureAttrKeyController", ["as" => "admin"]);

        //PStructureAttrValue
        Route::group(["prefix" => "p-structure-attr-value", "as" => "admin.p-structure-attr-value."],
            function () {
                Route::any("{p_structure_attr_value}/remove-image", ["as" => "remove-image", "uses" => "PStructureAttrValueController@removeImage"]);
            });
        Route::resource("p-structure-attr-value", "PStructureAttrValueController", ["as" => "admin"]);

        //Product
        Route::group(["prefix" => "product", "as" => "admin.product."],
            function () {
                Route::post("{product}/{key}/attach-attribute", ["as" => "attach-attribute", "uses" => "ProductController@attachAttribute"]);
                Route::post("{product}/{key}/detach-attribute", ["as" => "detach-attribute", "uses" => "ProductController@detachAttribute"]);
                Route::post("{product}/attach-colors", ["as" => "attach-colors", "uses" => "ProductController@attachColors"]);
                Route::post("{product}/attach-color", ["as" => "attach-color", "uses" => "ProductController@attachColor"]);
                Route::post("{product}/detach-color", ["as" => "detach-color", "uses" => "ProductController@detachColor"]);
                Route::post("{product}/attach-tags", ["as" => "attach-tags", "uses" => "ProductController@attachTags"]);
                Route::post("{product}/attach-tag", ["as" => "attach-tag", "uses" => "ProductController@attachTag"]);
                Route::post("{product}/detach-tag", ["as" => "detach-tag", "uses" => "ProductController@detachTag"]);
                Route::post("{product}/attach-badge", ["as" => "attach-badge", "uses" => "ProductController@attachBadge"]);
                Route::post("{product}/detach-badge", ["as" => "detach-badge", "uses" => "ProductController@detachBadge"]);
                Route::get("search", ["as" => "search", "uses" => "ProductController@search"]);
                Route::put("{product}/publish", ["as" => "publish", "uses" => "ProductController@publish"]);
                Route::put("{product}/clone", ["as" => "clone", "uses" => "ProductController@cloneModel"]);
                Route::get("{product}/models", ["as" => "models", "uses" => "ProductController@models"]);
                Route::get("cache-clear", ["as" => "cache-clear", "uses" => "DirectoryController@cacheClear"]);
            });
        Route::resource("product", "ProductController", ["as" => "admin"]);


        //ProductColor
        Route::group(["prefix" => "color", "as" => "admin.color."],
            function () {
                Route::any("{color}/remove-image", ["as" => "remove-image", "uses" => "ColorController@removeImage"]);
            });
        Route::resource("color", "ColorController", ["as" => "admin"]);

        //ProductPrice
        Route::resource("product-price", "ProductPriceController", ["as" => "admin"]);
        Route::resource("product-special-price", "ProductSpecialPriceController", ["as" => "admin"]);

        //ProductImage
        Route::group(["prefix" => "product-image", "as" => "admin.product-image."],
            function () {
                Route::any("{product_image}/set-as-main-image",
                    ["as" => "set-as-main-image", "uses" => "ProductImageController@setAsMainImage"]);
                Route::any("{product_image}/set-as-secondary-image",
                    ["as" => "set-as-secondary-image", "uses" => "ProductImageController@setAsSecondaryImage"]);
            });
        Route::resource("product-image", "ProductImageController", ["as" => "admin"]);

        //PStructure
        Route::group(["prefix" => "p-structure", "as" => "admin.p-structure."],
            function () {
                Route::post("{p_structure}/attach-attribute-key",
                    ["as" => "attach-attribute-key", "uses" => "PStructureController@attachAttributeKey"]);
                Route::post("{p_structure}/detach-attribute-key",
                    ["as" => "detach-attribute-key", "uses" => "PStructureController@detachAttributeKey"]);
                Route::get("{p_structure}/download-excel", [PStructureController::class, "downloadExcel"])->name("download-excel");
                Route::post("{p_structure}/upload-excel", [PStructureController::class, "uploadExcel"])->name("upload-excel");
            });
        Route::resource("p-structure", "PStructureController", ["as" => "admin"]);

        //ProductQuery
        Route::group(["prefix" => "product-query", "as" => "admin.product-query."],
            function () {

            });
        Route::resource("product-query", "ProductQueryController", ["as" => "admin"]);

        //ProductFilter
        Route::group(["prefix" => "product-filter", "as" => "admin.product-filter."],
            function () {

            });
        Route::resource("product-filter", "ProductFilterController", ["as" => "admin"]);

        //ProductPackage
        Route::group(["prefix" => "product-package", "as" => "admin.product-package."],
            function () {
                Route::get("edit/{product}", ["as" => "edit", "uses" => "ProductPackageController@edit"]);
                Route::put("update/{product}", ["as" => "update", "uses" => "ProductPackageController@update"]);
            });


        //WebPage
        Route::group(["prefix" => "web-page", "as" => "admin.web-page."],
            function () {
                Route::post("{web_page}/attach-tags", ["as" => "attach-tags", "uses" => "WebPageController@attachTags"]);
                Route::post("{web_page}/attach-tag", ["as" => "attach-tag", "uses" => "WebPageController@attachTag"]);
                Route::post("{web_page}/detach-tag", ["as" => "detach-tag", "uses" => "WebPageController@detachTag"]);
                Route::any("{web_page}/remove-image", ["as" => "remove-image", "uses" => "WebPageController@removeImage"]);
                Route::get("search", ["as" => "search", "uses" => "WebpageController@search"]);
            });
        Route::resource("web-page", "WebPageController", ["as" => "admin"]);

        //Gallery
        Route::resource("gallery", "GalleryController", ["as" => "admin"]);

        //GalleryItem
        Route::group(["prefix" => "gallery-item", "as" => "admin.gallery-item."],
            function () {
                Route::any("{gallery_item}/remove-image", ["as" => "remove-image", "uses" => "GalleryItemController@removeImage"]);
            });
        Route::resource("gallery-item", "GalleryItemController", ["as" => "admin"]);

        //Tag
        Route::group(["prefix" => "tag", "as" => "admin.tag."],
            function () {
                Route::any("query", ["as" => "query", "uses" => "TagController@query"]);
                Route::get("search", ["as" => "search", "uses" => "TagController@search"]);
            });
        Route::resource("tag", "TagController", ["as" => "admin"]);

        //Review
        Route::group(["prefix" => "review", "as" => "admin.review."],
            function () {
                Route::put("{review}/set-as-checked", ["as" => "set-as-checked", "uses" => "ReviewController@setAsChecked"]);
            });
        Route::resource("review", "ReviewController", ["as" => "admin"]);

        //robot-txt
        Route::group(["prefix" => "robot-txt", "as" => "admin.robot-txt."],
            function () {
                Route::any("{id}/remove", ["as" => "remove", "uses" => "RobotTxtController@destroy"]);
            });
        Route::resource("robot-txt", "RobotTxtController", ["as" => "admin"]);

        //ActionLog
        Route::group(["prefix" => "action-log", "as" => "admin.action-log."],
            function () {
                Route::get("filter", ["as" => "filter", "uses" => "ActionLogController@filter"]);
            });
        Route::resource("action-log", "ActionLogController", ["as" => "admin"]);

        //Translate
        Route::group(["prefix" => "model-translation", "as" => "admin.model-translation."], function () {
            Route::post("edit", ["as" => "edit", "uses" => "ModelTranslationController@edit"]);
            Route::put("update", ["as" => "update", "uses" => "ModelTranslationController@update"]);
        });

        //LiveReports
        Route::group(["prefix" => "live-reports", "as" => "admin.live-reports."], function () {
            Route::get("/", ["as" => "index", "uses" => "LiveReportsController@index"]);
        });

        //Null
        Route::any("null", ["as" => "admin.null", "uses" => "AdminController@nullMethod"]);

        Route::group([
            "namespace" => "Api\\V1",
            "prefix" => "api/v1",
            "as" => "admin.api.v1."
        ], function () {
            Route::any("product/query", ["middleware" => "json", "as" => "product.query", "uses" => "ProductController@query"]);
            Route::any("user/query", ["middleware" => "json", "as" => "user.query", "uses" => "UserController@query"]);
            Route::any("directory/query", ["middleware" => "json", "as" => "directory.query", "uses" => "DirectoryController@query"]);
            Route::any("invoice/query", ["middleware" => "json", "as" => "invoice.query", "uses" => "InvoiceController@query"]);

            Route::post("/clip-board/cut", ["as" => "clip-board.cut", "uses" => "ClipBoardController@doCut"]);
            Route::post("/clip-board/copy", ["as" => "clip-board.copy", "uses" => "ClipBoardController@doCopy"]);
            Route::post("/clip-board/paste", ["as" => "clip-board.paste", "uses" => "ClipBoardController@doPaste"]);

            Route::group(["prefix" => "live-reports", "as" => "live-reports."], function () {
                Route::get("daily-sales-amount", ["as" => "get-daily-sales-amount", "uses" => "LiveReportsController@getDailySalesAmount"]);
                Route::get("yesterday-sales-amount", ["as" => "get-yesterday-sales-amount", "uses" => "LiveReportsController@getYesterdaySalesAmount"]);
                Route::get("monthly-sales-amount", ["as" => "get-monthly-sales-amount", "uses" => "LiveReportsController@getMonthlySalesAmount"]);
                Route::get("yearly-sales-amount", ["as" => "get-yearly-sales-amount", "uses" => "LiveReportsController@getYearlySalesAmount"]);
                Route::get("previous-year-sales-amount", ["as" => "get-previous-year-sales-amount", "uses" => "LiveReportsController@getPreviousYearSalesAmount"]);
                Route::get("overall-bar-chart-data", ["as" => "get-overall-bar-chart-data", "uses" => "LiveReportsController@getOverallBarChartData"]);
                Route::get("overall-sales-bar-chart-data", ["as" => "get-overall-sales-bar-chart-data", "uses" => "LiveReportsController@getOverallSalesBarChartData"]);
                Route::get("overall-created-products-per-category", ["as" => "get-overall-created-products-per-category", "uses" => "LiveReportsController@getOverallCreatedProductsPerCategory"]);
                Route::get("monthly-categories-sales", ["as" => "get-monthly-categories-sales", "uses" => "LiveReportsController@getMonthlyCategoriesSales"]);
                Route::get("yearly-categories-sales", ["as" => "get-yearly-categories-sales", "uses" => "LiveReportsController@getYearlyCategoriesSales"]);
                Route::get("previous-year-categories-sales", ["as" => "get-previous-year-categories-sales", "uses" => "LiveReportsController@getPreviousYearCategoriesSales"]);
                Route::get("latest-customers", ["as" => "get-latest-customers", "uses" => "LiveReportsController@getLatestCustomers"]);
                Route::get("latest-payed-orders", ["as" => "get-latest-payed-orders", "uses" => "LiveReportsController@getLatestPayedOrders"]);
                Route::get("categories-availability", ["as" => "get-categories-availability", "uses" => "LiveReportsController@getCategoriesAvailability"]);
            });
        });

        //ShortLink
        Route::group(["prefix" => "short-link", "as" => "admin.short-link."],
            function () {
                Route::get("{short_link}/statistics", ["as" => "statistics", "uses" => "ShortLinkController@showStats"]);
            });
        Route::resource("short-link", "ShortLinkController", ["as" => "admin"]);

        //Badge
        Route::group(['prefix' => 'badge', 'as' => 'admin.badge.'],
            function () {
                Route::any("{badge}/remove-image", ["as" => "remove-image", "uses" => "BadgeController@removeImage"]);
            });
        Route::resource("badge", "BadgeController", ["as" => "admin"]);

        //classic search
        Route::get("/classic-search", ["as" => "admin.classic-search", "uses" => "AdminController@classicSearch"]);

        //Login As Any user
        Route::get("/login-as/{user}", ["as" => "admin.login-as",
            "uses" => "AdminController@loginAs"]);

        //Excel
        Route::group(["prefix" => "excel", "as" => "admin.excel."],
            function () {
                Route::get("export", ["as" => "export", "uses" => "ExcelController@export"]);
                Route::get("import/{model_name}", ["as" => "view-import", "uses" => "ExcelController@viewImport"]);
                Route::post("import", ["as" => "import", "uses" => "ExcelController@import"]);
                Route::get("import/get-sample/{model_name}", ["as" => "get-import-sample", "uses" => "ExcelController@getImportSample"]);
            });

        //Modal
        Route::group(['prefix' => 'modal', 'as' => 'admin.modal.'],
            function () {
                Route::any("{modal}/remove-image", ["as" => "remove-image", "uses" => "ModalController@removeImage"]);
            });
        Route::resource("modal", "ModalController", ["as" => "admin"]);

        //ModalRoute
        Route::resource("modal-route", "ModalRouteController", ["as" => "admin"]);
    });


//Customer auth routes
Route::group(
    [
        "namespace" => "Customer",
        "prefix" => "customer-auth",
        "as" => "customer-auth.",
        "middleware" => "customer-guest"
    ],
    function () {

        Route::get("auth/{type}", ["as" => "show-auth", "uses" => "AuthController@showAuth"]);
        Route::post("auth/mobile", ["as" => "do-mobile-auth", "uses" => "AuthController@doMobileAuth"]);
        Route::post("auth/email", ["as" => "do-email-auth", "uses" => "AuthController@doEmailAuth"]);

        Route::get("check/{type}/{value}", ["as" => "show-check", "uses" => "AuthController@showCheck"]);
        Route::post("check/{type}/{value}", ["as" => "do-check", "uses" => "AuthController@doCheck"]);

        Route::get("send-auth-confirm/{type}/{value}", ["as" => "send-auth-confirm",
            "uses" => "AuthController@sendAuthConfirm"]);

        Route::get("password-auth/{type}/{value}", ["as" => "show-password-auth",
            "uses" => "AuthController@showPasswordAuth"]);
        Route::post("password-auth/{type}/{value}", ["as" => "do-password-auth",
            "uses" => "AuthController@doPasswordAuth"]);

        Route::get("register/{type}/{value}", ["as" => "show-register", "uses" => "AuthController@showRegister"]);
        Route::post("register/{type}/{value}", ["as" => "do-register", "uses" => "AuthController@doRegister"]);
    }
);

//Private customer routes
Route::group(
    [
        "namespace" => "Customer",
        "prefix" => "customer",
        "middleware" => "customer",
        "as" => "customer."
    ],
    function () {
        Route::group(["prefix" => "profile", "as" => "profile."],
            function () {
                Route::get("/", ["as" => "index", "uses" => "ProfileController@index"]);
                Route::post("set-legal-person", ["as" => "set-legal-person", "uses" => "ProfileController@setLegalPerson"]);

                Route::get("change-password", ["as" => "show-change-password", "uses" => "ProfileController@showChangePassword"]);
                Route::post("change-password", ["as" => "do-change-password", "uses" => "ProfileController@doChangePassword"]);

                Route::get("edit-profile", ["as" => "show-edit-profile", "uses" => "ProfileController@showEditProfile"]);
                Route::post("update", ["as" => "update", "uses" => "ProfileController@update"]);
            });

        Route::group(["prefix" => "wish-list", "as" => "wish-list."],
            function () {
                Route::get("/", ["as" => "index", "uses" => "WishListController@index"]);
                Route::get("attach-product/{product}", ["as" => "attach-product", "uses" => "WishListController@attachProduct"]);
                Route::get("detach-product/{product}", ["as" => "detach-product", "uses" => "WishListController@detachProduct"]);
            });

        Route::group(["prefix" => "need-list", "as" => "need-list."],
            function () {
                Route::get("attach-product/{product}", ["as" => "attach-product", "uses" => "NeedListController@attachProduct"]);
                Route::get("detach-product/{product}", ["as" => "detach-product", "uses" => "NeedListController@detachProduct"]);
            });

        Route::group(["prefix" => "cart", "as" => "cart."],
            function () {
                Route::get("show", ["as" => "show", "uses" => "CartController@show"]);
                Route::get("attach-product/{product}", ["as" => "attach-product", "uses" => "CartController@attachProduct"]);
                Route::get("detach-product/{product}", ["as" => "detach-product", "uses" => "CartController@detachProduct"]);
                Route::get("update-count/{product}", ["as" => "update-count", "uses" => "CartController@updateCount"]);
                Route::delete("remove-deactivated", ["as" => "remove-deactivated", "uses" => "CartController@removeDeactivated"]);
                Route::delete("remove-all", ["as" => "remove-all", "uses" => "CartController@removeAll"]);
            });

        Route::group(["prefix" => "rate", "as" => "rate."],
            function () {
                Route::get("product/{product}", ["as" => "product", "uses" => "RatingController@product"]);
            });

        Route::group(["prefix" => "address", "as" => "address."],
            function () {
                Route::get("create", ["as" => "create", "uses" => "AddressController@create"]);
                Route::post("store", ["as" => "store", "uses" => "AddressController@store"]);
                Route::get("edit/{customer_address}", ["as" => "edit", "uses" => "AddressController@edit"]);
                Route::post("update/{customer_address}", ["as" => "update", "uses" => "AddressController@update"]);
                Route::post("delete/{address}", ["as" => "destroy", "uses" => "AddressController@delete"]);
                Route::post("set-main", ["as" => "set-main", "uses" => "AddressController@setMain"]);
            });

        Route::group(["prefix" => "invoice", "as" => "invoice.", "middleware" => "customer-init"],
            function () {
                Route::get("/", ["as" => "index", "uses" => "InvoiceController@index"]);
                Route::post("submit-cart", ["as" => "submit-cart", "uses" => "InvoiceController@submitCart"]);
                Route::get("submit-cart", ["uses" => "InvoiceController@submitCart"]); //this is used for redirection
                Route::get("shipment", ["as" => "show-shipment", "uses" => "InvoiceController@showShipment"]);
                Route::post("shipment", ["as" => "save-shipment", "uses" => "InvoiceController@saveShipment"]);
                Route::get("payment", ["as" => "show-payment", "uses" => "InvoiceController@showPayment"]);
                Route::post("payment", ["as" => "save-payment", "uses" => "InvoiceController@savePayment"]);
                Route::get("checkout/{invoice}", ["as" => "show-checkout", "uses" => "InvoiceController@showCheckout"]);
                Route::any("pay-online/{invoice}", ["as" => "pay-online", "uses" => "InvoiceController@payOnline"]);
                Route::get("survey/{invoice}", ["as" => "survey.show", "uses" => "InvoiceController@showSurvey"]);
                Route::post("enable/{invoice}", ["as" => "enable", "uses" => "InvoiceController@enable"]);
                Route::post("check-discount-code",
                    ["as" => "check-discount-code", "uses" => "InvoiceController@checkDiscountCode"]);
            });

        Route::group(["prefix" => "meta-item", "as" => "meta-item."],
            function () {
                Route::get("/{cart_row}/{customer_meta_category}", ["as" => "create", "uses" => "MetaItemController@create"]);
                Route::post("/{cart_row}/{customer_meta_category}", ["as" => "store", "uses" => "MetaItemController@store"]);
                Route::delete("/{customer_meta_item}", ["as" => "destroy", "uses" => "MetaItemController@destroy"]);
            });
    }
);

//Public customer routes
Route::group(
    [
        "namespace" => "Customer",
        "prefix" => "customer",
        "as" => "customer."
    ],
    function () {
        Route::get("local-cart", ["as" => "show-local-cart", "uses" => "CartController@showLocal"]);
        Route::delete("cart/remove-local", ["as" => "cart.remove-local", "uses" => "CartController@removeLocal"]);
        Route::get("email-confirmation", ["as" => "email-confirmation", "uses" => "AuthController@emailConfirmation"]);
        Route::post("location", ["as" => "location.store", "uses" => "LocationController@store"]);
    }
);
Route::group(
    [
        "prefix" => "comparison",
        "as" => "comparison."
    ],
    function () {
        Route::get("show", ["as" => "show",
            "uses" => "ComparisonController@show"]);
        Route::get("init/{product}", ["as" => "init",
            "uses" => "ComparisonController@init"]);
        Route::get("add-product/{product}", ["as" => "add-product",
            "uses" => "ComparisonController@add"]);
        Route::get("remove-product/{product}", ["as" => "remove-product",
            "uses" => "ComparisonController@remove"]);
    }
);

Route::group(
    [
        "prefix" => "health",
        "as" => "health."
    ],
    function (){
        Route::get("dbversion", [
            "as" => "dbversion",
            "uses" => "HealthController@getDBVersion"
        ]);
    }
);

//Public routes
Route::post("/message/save", ["as" => "message-save", "uses" => "MessageController@saveMessage"]);
Route::post("/newsletter/subscribe", ["as" => "newsletter", "uses" => "NewsletterController@save"]);
Route::get("/", ["as" => "public.home", "uses" => "HomeController@main"]);
Route::get("/search", ["as" => "public.search", "uses" => "HomeController@search"]);
Route::get("/product/{product}", ["as" => "public.view-product", "uses" => "HomeController@showProduct"])->where('product', '[0-9]+');
Route::get("/product/{product}/{any}", "HomeController@showProduct")->where("any", ".*")->where('product', '[0-9]+');
Route::get("/blog/{article}", ["as" => "public.view-blog", "uses" => "HomeController@showBlog"]);
Route::get("/blog/{article}/{any}", "HomeController@showBlog")->where("any", ".*");
Route::get("/{any}", "HomeController@main")->where("any", ".*");

