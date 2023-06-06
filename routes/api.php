<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        "middleware" => ["json"],
        "namespace" => "Api\\V1",
        "prefix" => "v1/",
        "as" => "api.v1.",
    ],
    function () {
        Route::group(["prefix" => "shop", "as" => "shop."],
            function () {
                Route::get("filters", ["as" => "get-filters", "uses" => "ShopController@getFilters"]);
                Route::get("search", ["as" => "search", "uses" => "ShopController@search"]);
                Route::get("product", ["as" => "get-product", "uses" => "ShopController@getProduct"]);
                Route::get("product-rates", ["as" => "get-product-rates", "uses" => "ShopController@getProductRates"]);
                Route::get("filter-products", ["as" => "filter-products", "uses" => "ShopController@filterProducts"]);
                Route::get("/", "ShopController@__call");
                Route::get("{any}", "ShopController@__call")->where("any", ".*");
            });

        Route::group(["prefix" => "product", "as" => "product."],
            function () {
                Route::get("/", ["as" => "index", "uses" => "ProductController@index"]);
                Route::get("/torob", ["as" => "index", "uses" => "ProductController@torob"]);
            });

        Route::group(["prefix" => "location", "as" => "location."],
            function () {
                Route::get("get-states", ["as" => "get-states", "uses" => "LocationController@getStates"]);
                Route::get("get-cities", ["as" => "get-cities", "uses" => "LocationController@getCities"]);
                Route::get("get-districts", ["as" => "get-districts", "uses" => "LocationController@getDistricts"]);
            });
    }
);
