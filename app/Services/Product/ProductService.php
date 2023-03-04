<?php

namespace App\Services\Product;

use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Directory;
use App\Models\PAttr;
use App\Models\Product;
use App\Models\PStructure;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;
use Exception;
use Illuminate\Support\Collection;

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
}
