<?php

namespace App\Services\Product;

use App\Models\PAttr;
use App\Models\Product;
use App\Models\PStructure;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;
use App\Services\PStructure\PStructureService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class ProductExporterService {

    /**
     * @param Collection|array $products
     * @return int[]
     */
    private static function getProductIdsFromProductList(Collection|array $products): array {
        return array_map(
            function (Product $product) {
                return $product->id;
            }, $products->all());
    }

    public static function exportDataArray(PStructure $p_structure): array {
        $products = ProductService::getAllProductsByPStructure($p_structure);
        $product_ids = static::getProductIdsFromProductList($products);
        $p_attrs = PStructureService::getAllPAttrsByProductsIds($product_ids);

        $key_ids = [];
        $value_ids = [];
        $p_attr_product_id_map = [];

        foreach ($p_attrs as $p_attr) {
            if (!isset($p_attr_product_id_map[$p_attr->product_id])) {
                $p_attr_product_id_map[$p_attr->product_id] = [];
            }

            if (!isset($p_attr_product_id_map[$p_attr->product_id][$p_attr->p_structure_attr_key_id])) {
                $p_attr_product_id_map[$p_attr->product_id][$p_attr->p_structure_attr_key_id] = [];
            }

            $p_attr_product_id_map[$p_attr->product_id][$p_attr->p_structure_attr_key_id] = $p_attr;

            if (!in_array($p_attr->p_structure_attr_key_id, $key_ids))
                $key_ids[] = $p_attr->p_structure_attr_key_id;

            if (!in_array($p_attr->p_structure_attr_value_id, $value_ids))
                $value_ids[] = $p_attr->p_structure_attr_value_id;

        }

        $keys = PStructureService::getAllPStructureAttrKeysById($key_ids);
        $key_titles = array_map(function (PStructureAttrKey $key) {
            return $key->title;
        }, $keys);


        $values = PStructureService::getAllPStructureAttrValuesById($value_ids);
        $values_map = [];
        foreach ($values as $value) {
            $values_map[$value->id] = $value;
        }

        $tmp_product = new Product();
        $base_columns = Schema::getColumnListing($tmp_product->getTable());
        $extra_columns = $key_titles;
        $columns = [
            ...$base_columns,
            ...$extra_columns
        ];

        $rows = [];
        foreach ($products as $product) {
            $product_attrs = $p_attr_product_id_map[$product->id] ?? [];

            $rows[] = [
                ...array_map(
                    function (string $col_name) use ($product) {
                        return $product->$col_name;
                    }, $base_columns),

                ...array_map(function (PStructureAttrKey $key) use ($product_attrs, $values_map) {
                    /**
                     * @var PAttr[] $key_attrs
                     */
                    $key_attrs = $product_attrs[$key->id] ?? [];
                    $values = [];
                    foreach ($key_attrs as $key_attr) {
                        $values[] = $values_map[$key_attr->p_structure_attr_value_id];
                    }

                    return implode(", ", array_map(function (PStructureAttrValue $value) {
                        return $value->name;
                    }, $values));
                }, $keys)
            ];
        }

        return compact("columns", "rows");
    }
}
