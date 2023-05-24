<?php

namespace App\Services\Product;

use App\Models\PAttr;
use App\Models\Product;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;

class ProductModelService
{
    public static function getProductModelOptionsMultiLevel(Product $product): array {
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
        foreach ($values as $value) {
            $values_map_id[$value->id] = $value;
        }

        $result = [];
        $tmp_row_set = [];
        $tmp_key_id = null;
        $tmp_value_index_map = [];
        $tmp_product_value_map = [[]];
        $row_index = 0;
        foreach ($p_attrs as $p_attr) {
            if ($tmp_key_id !== $p_attr->p_structure_attr_key_id) {
                if (count($tmp_row_set["values"] ?? []) > 0) {
                    $result[] = $tmp_row_set;
                    $row_index += 1;
                    $tmp_product_value_map[] = [];
                }
                $tmp_row_set = [
                    "key" => $keys_map_id[$p_attr->p_structure_attr_key_id],
                    "values" => [],
                    "value_ids" => []
                ];
                $tmp_key_id = $p_attr->p_structure_attr_key_id;
                $tmp_value_index_map = [];
            }

            if (!array_key_exists($p_attr->product_id, $tmp_product_value_map[$row_index])) {
                $tmp_product_value_map[$row_index][$p_attr->product_id] = [];
            }
            $tmp_product_value_map[$row_index][$p_attr->product_id][$p_attr->p_structure_attr_value_id] = true;

            if (!in_array($p_attr->p_structure_attr_value_id, $tmp_row_set["value_ids"])) {
                $tmp_index = count($tmp_row_set["values"]);
                $tmp_row_set["values"][] = [
                    "value" => $values_map_id[$p_attr->p_structure_attr_value_id],
                    "parent_values" => [],
                    "products" => [],
                    "product_ids" => []
                ];
                $tmp_row_set["value_ids"][] = $p_attr->p_structure_attr_value_id;
                $tmp_value_index_map[$p_attr->p_structure_attr_value_id] = $tmp_index;
            }


            if (!in_array(
                $p_attr->product_id,
                $tmp_row_set["values"][$tmp_value_index_map[$p_attr->p_structure_attr_value_id]]["product_ids"]
            )) {
                $tmp_row_set["values"][$tmp_value_index_map[$p_attr->p_structure_attr_value_id]]["product_ids"][] = $p_attr->product_id;
                $tmp_row_set["values"][$tmp_value_index_map[$p_attr->p_structure_attr_value_id]]["products"][] = $same_model_products_map_id[$p_attr->product_id];
                if ($row_index > 0) {
                    $tmp_row_set["values"][$tmp_value_index_map[$p_attr->p_structure_attr_value_id]]["parent_values"] =
                        array_unique([
                            ...$tmp_row_set["values"][$tmp_value_index_map[$p_attr->p_structure_attr_value_id]]["parent_values"],
                            ...array_keys($tmp_product_value_map[$row_index - 1][$p_attr->product_id] ?? [])
                        ]);
                }
            }
        }
        if (count($tmp_row_set["values"] ?? []) > 0) {
            $result[] = $tmp_row_set;
        }

        return $result;
    }

    public static function getProductModelOptionsSingleLevel(Product $product): array {
        return [];
    }

}
