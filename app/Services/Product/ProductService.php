<?php

namespace App\Services\Product;

use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Directory;
use App\Models\Product;
use App\Models\PStructure;
use Exception;
use Illuminate\Support\Collection;

class ProductService {

    private const UPDATE_EXCLUDE_ATTRIBUTES = [
        "directory_id",
        "is_package",
        "color_code",
        "average_rating",
        "cmc_id"
    ];

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

    public static function chunkAllProductsByPStructure(PStructure $p_structure, int $count, callable $callback): bool {
        return $p_structure->products()->chunk($count, $callback);
    }

    public static function createProductFromAttributesArray(array $attributes, Directory $directory): Product {
        $product = Product::create($attributes);
        $product->attachFileTo($directory);
        $product->createReview();

        if ($attributes["is_package"] ?? false) {
            $product->productPackage()->create([]);
        }

        return $product;
    }

    public static function updateProductFromAttributesArray(Product $product, array $attributes): Product {
        $clean_attributes = [];

        foreach ($attributes as $attribute_key => $attribute_value) {
            if (in_array($attribute_key, static::UPDATE_EXCLUDE_ATTRIBUTES))
                continue;

            $clean_attributes[$attribute_key] = $attribute_value;
        }

        $product->update($clean_attributes);
        $product->updateReview();

        return $product;
    }
}
