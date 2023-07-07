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

class ProductExporterService
{

    private const EXCLUDE_COLUMNS = [
        "extra_properties",
        "description",
        "created_at",
        "updated_at",
        "color_code",
        "models_count",
        "discount_group_id",
        "structure_sort_score",
        "important_at",
        "rates_count",
        "toll_amount",
        "tax_amount",
        "pure_price",
        "cmc_id",
        "notice",
        "average_rating"
    ];

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
        ini_set("memory_limit", -1);
        ini_set("max_execution_time", -1);

        $products = ProductService::getAllProductsByPStructure($p_structure);
        $product_ids = static::getProductIdsFromProductList($products);
        $p_attrs = PStructureService::getAllPAttrsByProductsIds($product_ids);

        $keys = $p_structure->attributeKeys()->get();
        $value_ids = [];
        $p_attr_product_id_map = [];

        foreach ($p_attrs as $p_attr) {
            if (!isset($p_attr_product_id_map[$p_attr->product_id])) {
                $p_attr_product_id_map[$p_attr->product_id] = [];
            }

            if (!isset($p_attr_product_id_map[$p_attr->product_id][$p_attr->p_structure_attr_key_id])) {
                $p_attr_product_id_map[$p_attr->product_id][$p_attr->p_structure_attr_key_id] = [];
            }

            $p_attr_product_id_map[$p_attr->product_id][$p_attr->p_structure_attr_key_id][] = $p_attr;

            if (!in_array($p_attr->p_structure_attr_value_id, $value_ids))
                $value_ids[] = $p_attr->p_structure_attr_value_id;

        }

        $key_titles = array_map(function (PStructureAttrKey $key) {
            return $key->title;
        }, $keys->all());


        $values = PStructureService::getAllPStructureAttrValuesByIds($value_ids);
        $values_map = [];
        foreach ($values as $value) {
            $values_map[$value->id] = $value;
        }

        $tmp_product = new Product();
        $base_columns = array_filter(Schema::getColumnListing($tmp_product->getTable()), function (string $column) {
            return !in_array($column, static::EXCLUDE_COLUMNS);
        });
        $key_columns = $key_titles;
        $columns = [
            ...$base_columns,
            ...$key_columns
        ];
        $extra_columns = [];
        $count_of_current_columns = count($columns);

        $rows = [];
        foreach ($products as $product) {
            $product_attrs = $p_attr_product_id_map[$product->id] ?? [];

            $row = [
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
                }, $keys->all()),
                ...array_fill(0, count($extra_columns), "")
            ];

            $extra_properties = $product->getExtraProperties();
            foreach ($extra_properties as $item) {
                $ex_col_name = "e:" . $item->key;
                $ex_col_value = $item->value;

                $col_index = array_search($ex_col_name, $extra_columns);

                if ($col_index !== false) {
                    $row[$col_index + $count_of_current_columns] = $ex_col_value;
                } else {
                    $extra_columns[] = $ex_col_name;
                    $row[] = $ex_col_value;
                }
            }


            $rows[] = $row;
        }

        $columns = [
            ...$columns,
            ...$extra_columns
        ];

        return compact("columns", "rows");
    }
}
