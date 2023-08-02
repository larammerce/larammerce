<?php

namespace App\Services\Product;

use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Color;
use App\Models\Directory;
use App\Models\PAttr;
use App\Models\Product;
use App\Models\PStructure;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;
use App\Services\Directory\DirectoryService;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use JetBrains\PhpStorm\ArrayShape;

class ProductService
{
    /**
     * @throws ProductNotFoundException
     */
    public static function findProductById(int $product_id): Product {
        try {
            return Product::findOrFail($product_id);
        } catch (Exception $e) {
            throw new ProductNotFoundException("The product with id `{$product_id}` not found in the database.");
        }
    }

    /**
     * @param PStructure $p_structure
     * @return Collection|Product[]
     */
    public static function getAllProductsByPStructure(PStructure $p_structure): Collection|array {
        return $p_structure->products()->orderBy("id", "ASC")->get();
    }

    public static function createProductFromAttributesArray(array $attributes, Directory $directory): Product {
        $product = Product::create($attributes);
        $product->attachFileTo($directory);
        $product->createReview();

        if (!isset($attributes["model_id"])) {
            $product->update([
                "model_id" => $product->id
            ]);
        }

        if ($attributes["is_package"] ?? false) {
            $product->productPackage()->create([]);
        }

        return $product;
    }

    public static function updateProductFromAttributesArray(Product $product, array $attributes): Product {
        $product->update($attributes);
        $product->updateReview();
        return $product;
    }

    public static function attachAttributeToProduct(Product $product, PStructureAttrKey $key, PStructureAttrValue $value): void {
        if ($product->pAttributes()
                ->where('p_structure_attr_key_id', '=', $key->id)
                ->where('p_structure_attr_value_id', '=', $value->id)
                ->count() > 0) {
            return;
        }

        PAttr::create([
            "product_id" => $product->id,
            "p_structure_attr_key_id" => $key->id,
            "p_structure_attr_value_id" => $value->id,
        ]);

        if ($key->is_sortable) {
            $product->buildStructureSortScore($key);
        }
    }

    public static function detachAttributeFromProduct(Product $product, PStructureAttrKey $key, PStructureAttrValue $value): void {
        $product->pAttributes()
            ->where('p_structure_attr_key_id', '=', $key->id)
            ->where('p_structure_attr_value_id', '=', $value->id)
            ->delete();

        if ($key->is_sortable) {
            $product->buildStructureSortScore($key);
        }
    }

    /**
     * @param integer[] $products_ids
     * @return array
     */
    #[ArrayShape(["price_range" => "array", "keys" => "array", "colors" => "mixed", "directories" => "array"])]
    public static function getFilterData(Collection|array $products_ids): array {
        $price_range = [];
        if (count(is_countable($products_ids) ? $products_ids : []) == 0) {
            $price_range["min"] = 0;
            $price_range["max"] = 0;
            $keys = [];
            $colors = [];
            $directories = [];
        } else {
            $price_range["min"] = Product::whereIn('id', $products_ids)->min('latest_price');
            $price_range["max"] = Product::whereIn('id', $products_ids)->max('latest_price');

            $keys = PStructureAttrKey::getFilterBladeKeys($products_ids);
            $directories = DirectoryService::buildDirectoryGraph(Directory::join("directory_product", function ($join) use ($products_ids) {
                $join->on("directories.id", "=", "directory_product.directory_id")
                    ->whereIn("product_id", $products_ids);
            })->groupBy("id")->orderBy("repeat_count", "DESC")
                ->selectRaw(DB::raw("directories.*, count(id) as repeat_count"))->get());

            $colors = Color::whereHas('products', function ($query) use ($products_ids) {
                $query->whereIn('product_id', $products_ids);
            })->get();
        }

        if ($price_range["min"] == $price_range["max"])
            $price_range["max"] += 10000;
        return [
            "price_range" => $price_range,
            "keys" => $keys,
            "colors" => $colors,
            "directories" => $directories
        ];
    }
}
