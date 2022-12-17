<?php

namespace App\Services\Product;

use App\Models\PAttr;
use App\Models\Product;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;

class ProductModelService {
    public static function getProductModelOptions(Product $product) {
        /** @var PStructureAttrKey[] $keys */
        $keys = $product->productStructure?->attributeKeys()
            ->where("is_model_option", true)
            ->orderBy("priority", "ASC")
            ->get()
            ->all();
        /** @var PStructureAttrKey[] $keys_map_id */
        $keys_map_id = [];
        /** @var int[] $key_ids */
        $key_ids = [];
        foreach ($keys as $key) {
            $key_ids[] = $key->id;
            $keys_map_id[$key->id] = $key;
        }

        /** @var Product[] $same_model_products */
        $same_model_products = Product::where("model_id", $product->model_id)->get()->all();
        /** @var Product[] $same_model_products_map_id */
        $same_model_products_map_id = [];
        /** @var int[] $same_model_product_ids */
        $same_model_product_ids = [];
        foreach ($same_model_products as $same_model_product) {
            $same_model_product_ids[] = $same_model_product->id;
            $same_model_products_map_id[$same_model_product->id] = $same_model_product;
        }

        /** @var PAttr[] $p_attrs */
        $p_attrs = PAttr::whereIn("product_id", $same_model_product_ids)
            ->whereIn("p_structure_attr_key_id", $key_ids)
            ->orderBy("p_structure_attr_key_id")
            ->get()
            ->all();
        /** @var int[] $value_ids */
        $value_ids = array_map(function (PAttr $p_attr) {
            return $p_attr->p_structure_attr_value_id;
        }, $p_attrs);
        /** @var PStructureAttrValue[] $values */
        $values = PStructureAttrValue::whereIn("id", $value_ids)->get()->all();
        /** @var PStructureAttrValue[] $values_map */
        $values_map_id = [];
        $values_map_key_id = [];
        foreach ($values as $value) {
            $values_map_id[$value->id] = $value;
            $values_map_key_id[$value->p_structure_attr_key_id] = $value;
        }


    }
}