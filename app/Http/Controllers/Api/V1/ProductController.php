<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class ProductController extends BaseController{

    public function index()
    {
        $products = Product::query()->paginate();
        return compact("products");
    }

    public function torob(){
        try{
            $products = Product::query()
                ->paginate(100)->toArray();

            foreach ($products["data"] as $index => $product_array) {
                $tmp_array = [];
                $tmp_array["product_id"] = $product_array["id"];
                $tmp_array["page_url"] = $product_array["url"];
                $tmp_array["price"] = $product_array["latest_price"];
                $tmp_array["availability"] = $product_array["is_active"] ? "instock" : "-";
                $tmp_array["old_price"] = $product_array["previous_price"];
                $products["data"][$index] = $tmp_array;
            }
        }catch (Exception $e){
            return compact("e");
        }
        return compact("products");
    }
}