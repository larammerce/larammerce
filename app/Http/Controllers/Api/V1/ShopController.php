<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/19/17
 * Time: 11:39 AM
 */

namespace App\Http\Controllers\Api\V1;

use App\Enums\Directory\DirectoryType;
use App\Models\Directory;
use App\Models\Product;
use App\Models\ProductFilter;
use App\Models\ProductQuery;
use App\Models\Rate;
use App\Services\Product\ProductService;
use App\Utils\Common\MessageFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Class ShopController
 * @package App\Http\Controllers\Api\V1
 */
class ShopController extends BaseController
{
    /**
     * @rules(directory_id="required|exists:directories,id")
     * @description(return="priceRange, keys[], colors[]", request_method="GET",
     *     comment="this api will return priceRange, keys(with nested values) and colors for your filter")
     */
    public function getFilters()
    {
        $directory = Directory::find(request()->get("directory_id"));
        $products_ids = $directory->leafProducts()->mainModels()->visible()->pluck("products.id")->toArray();
        $filter_data = ProductService::getFilterData($products_ids);
        return MessageFactory::jsonResponse([], 200, [
            "price_range" => $filter_data["priceRange"],
            "keys" => $filter_data["keys"],
            "colors" => $filter_data["colors"]
        ]);
    }

    /**
     * @rules(directory_id="exists:directories,id", price_range="array", values="array|min:1",
     *     colors="array",values.*.*="exists:p_structure_attr_values,id", colors.*="exists:colors,id",
     *     sort.method="in:ASC,DESC,asc,desc", product_ids.*="exists:products,id", product_query_id="exists:product_queries,id",
     *     product_filter_id="exists:product_filters,id", product_query_identifier="exists:product_queries,identifier",
     *     product_filter_identifier="exists:product_filters,identifier")
     * @description(return="products[]", request_method="POST", comment="this api will filter products according to selected options")
     */
    public function filterProducts()
    {
        if (request()->has("product_query_id")) {
            $product_query = ProductQuery::find(request()->get("product_query_id"));
            $products = $product_query->getQuery()->selectRaw(DB::raw("IF(products.latest_price > 0, 1 , 0) as has_price, products.*"));
        } else if (request()->has("product_query_identifier")) {
            $product_query = ProductQuery::findByIdentifier(request()->get("product_query_identifier"));
            $products = $product_query->getQuery()->selectRaw(DB::raw("IF(products.latest_price > 0, 1 , 0) as has_price, products.*"));
        } else if (request()->has("product_filter_id")) {
            $product_filter = ProductFilter::find(request()->get("product_filter_id"));
            $products = $product_filter->getQuery()->selectRaw(DB::raw("IF(products.latest_price > 0, 1 , 0) as has_price, products.*"));
        } else if (request()->has("product_filter_identifier")) {
            $product_filter = ProductFilter::find(request()->get("product_filter_identifier"));
            $products = $product_filter->getQuery()->selectRaw(DB::raw("IF(products.latest_price > 0, 1 , 0) as has_price, products.*"));
        } else if (request()->has("directory_id")) {
            $products = Directory::find(request("directory_id"))->leafProducts()->selectRaw(DB::raw("IF(products.latest_price > 0, 1 , 0) as has_price"));
        } else {
            $products = Product::query()->selectRaw(DB::raw("IF(products.latest_price > 0, 1 , 0) as has_price, products.*"));
        }

        if (request()->has("query"))
            $products = $products->search(request("query"));

        if (request()->has("product_ids"))
            $products = $products->whereIn("id", request("product_ids"));

        if (request()->has("p_structure_id"))
            $products = $products->where("p_structure_id", request("p_structure_id"));

        if(config("cms.general.site.only_main_models")){
            $products = $products->mainModels()->visible();
        }else{
            $products = $products->visible();
        }

        if (request()->has("price_range")) {
            $price_range = request("price_range");
            if (count(is_countable($price_range) ? $price_range : []) == 2) {
                if ($price_range[0] > $price_range[1])
                    $price_range = array_reverse($price_range);

                $products = $products->whereBetween("latest_price", $price_range);
            }
        }

        if (request()->has("values")) {
            $values = request()->get("values");
            if (count(is_countable($values) ? $values : []) > 0)
                foreach ($values as $value) {
                    $products = $products->whereHas("pAttributes", function ($query) use ($value) {
                        $query->whereIn("p_structure_attr_value_id", $value);
                    });
                }
            else
                $products = $products->whereHas("pAttributes", function ($query) use ($values) {
                    $query->whereIn("p_structure_attr_value_id", $values);
                });
        }

        if (request()->has("colors"))
            $products = $products->whereHas("colors", function ($query) {
                $query->whereIn("color_id", request("colors"));
            });

        $products = $products->orderBy("is_active", "DESC")->orderBy("has_price", "DESC");
        $sort_data = explode(":", config("cms.general.product_sort"));
        if (request()->has("sort")) {
            $sort_data[0] = request("sort.field");
            $sort_data[1] = request("sort.method");
        }
        if (Schema::hasColumn("products", $sort_data[0]) and in_array(strtolower($sort_data[1]), ["asc", "desc"])) {
            $products = $products->orderBy($sort_data[0], $sort_data[1]);
        } else {
            $products = $products->orderBy("priority", "ASC");
        }
        return $products->paginate(Product::getFilterPaginationCount());
    }

    /**
     * @rules(query="required", directories_count="numeric|min:1|max:10", products_count="numeric|min:1|max:20")
     * @description(return="directories[],products[]", request_method="GET", comment="")
     */
    public function search()
    {
        $products_count = request()->has("products_count") ? request()->get("products_count") : 4;
        $directories_count = request()->has("directories_count") ? request()->get("directories_count") : 3;
        $products = Product::search(request()->get("query"))->mainModels()->visible();

        $directories = Directory::where("content_type", DirectoryType::PRODUCT)->search(request()->get("query"), 1)
            ->take($directories_count)->get();

        foreach ($directories as $directory) {
            $directory->title = implode(" > ", array_map(function ($iter_directory) {
                return $iter_directory["title"];
            }, $directory->getParentDirectories()->toArray()));
        }

        $products = $products->orderBy("priority", "ASC")->take($products_count)->get();

        return compact("products", "directories");
    }

    /**
     * @rules(id="required|exists:products")
     */
    public function getProduct()
    {
        $product = Product::visible()->where("id", request()->get("id"))->first();
        if ($product === null)
            abort(404);
        $product->load(["directory", "colors", "images", "tags", "pAttributes"]);
        return compact("product");
    }

    /**
     * @rules(id="required|exists:products")
     */
    public function getProductRates()
    {

        $product = Product::visible()->where("id", request()->get("id"))->first();
        if ($product === null)
            abort(404);
        return $product->approvedRates()->with(["customerUser.user"])->paginate(Rate::getPaginationCount());
    }
}
