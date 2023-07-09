<?php

namespace App\Services\Product;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Exceptions\Product\ProductNotFoundException;
use App\Models\PAttr;
use App\Models\Product;
use App\Models\PStructure;
use App\Models\PStructureAttrKey;
use App\Services\Directory\DirectoryService;
use App\Services\PStructure\PStructureService;
use Illuminate\Support\Facades\Schema;

class ProductImporterService
{
    private const CASTS = [
        "latest_price" => "int",
        "latest_special_price" => "int",
        "count" => "int",
        "min_purchase_count" => "int",
        "previous_price" => "int"
    ];

    private const UPDATE_EXCLUDE_ATTRIBUTES = [
        "directory_id",
        "is_package",
        "color_code",
        "average_rating",
        "cmc_id"
    ];

    private static function getMainAttributesFromDataArray(array $data_array): array {
        $result = [];
        $tmp_product = new Product();

        foreach ($tmp_product->getFillable() as $fillable) {
            if (!key_exists($fillable, $data_array) or $data_array[$fillable] === null) {
                continue;
            }
            $value = $data_array[$fillable];

            if (key_exists($fillable, self::CASTS)) {
                if (self::CASTS[$fillable] == "int") {
                    $value = (int)$value;
                }
            }

            $result[$fillable] = $value;
        }

        return $result;
    }

    private static function getPStructureAttributesFromDataArray(array $data_array): array {
        $values_title_map = [];
        $keys_title_map = [];
        $result = [];
        $tmp_product = new Product();
        $product_table_columns = Schema::getColumnListing($tmp_product->getTable());


        $key_order = 0;
        foreach ($data_array as $key_title => $value_title) {
            if ($key_title == null)
                continue;

            if (in_array($key_title, $product_table_columns))
                continue;
            if (str_starts_with($key_title, "e:") or str_starts_with($key_title, "E:"))
                continue;

            if (key_exists($key_title, $keys_title_map))
                $key = $keys_title_map[$key_title];
            else {
                $key = PStructureService::findOrCreateKeyByTitle($key_title, $key_order++);
                $keys_title_map[$key_title] = $key;
            }

            $exploded_value_titles = array_map(function (string $title) {
                return trim($title ?? "");
            }, explode(",", $value_title));
            $values = [];
            foreach ($exploded_value_titles as $exploded_value_title) {
                if(strlen($exploded_value_title) == 0)
                    continue;
                if (key_exists($exploded_value_title, $values_title_map))
                    $value = $values_title_map[$exploded_value_title];
                else {
                    $value = PStructureService::findOrCreateValueByName($exploded_value_title, $key);
                    $values_title_map[$exploded_value_title] = $value;
                }
                $values[] = $value;
            }

            $result[] = [
                "key" => $key,
                "values" => $values
            ];
        }

        return $result;
    }

    private static function getExtraPropertiesFromDataArray(array $data_array): array {
        $result = [];
        $priority = 0;
        foreach ($data_array as $key => $value) {
            if ($value == null or $key == null)
                continue;

            if (str_starts_with($key, "e:") or str_starts_with($key, "E:")) {
                $key = str_replace(["e:", "E:"], "", $key);
                $result[] = [
                    "key" => $key,
                    "value" => $value,
                    "priority" => $priority++,
                    "type" => 0
                ];
            }
        }

        return $result;
    }


    /**
     * @throws ProductNotFoundException
     * @throws DirectoryNotFoundException
     */
    public static function importFromDataArray(PStructure $p_structure, array $data_array): Product {
        ini_set("memory_limit", -1);
        ini_set("max_execution_time", -1);

        $product_id = $data_array["id"];
        $directory_id = $data_array["directory_id"];

        $main_attributes = static::getMainAttributesFromDataArray($data_array);
        $p_structure_attributes = static::getPStructureAttributesFromDataArray($data_array);
        $extra_properties = static::getExtraPropertiesFromDataArray($data_array);
        $main_attributes["extra_properties"] = $extra_properties;
        $main_attributes["p_structure_id"] = $p_structure->id;
        $p_structure_value_ids = [];

        if ($product_id == null) {
            $directory = DirectoryService::findDirectoryById($directory_id);
            $product = ProductService::createProductFromAttributesArray($main_attributes, $directory);
        } else {
            $clean_attributes = [];
            foreach ($main_attributes as $attribute_key => $attribute_value) {
                if (in_array($attribute_key, static::UPDATE_EXCLUDE_ATTRIBUTES))
                    continue;

                $clean_attributes[$attribute_key] = $attribute_value;
            }
            $product = ProductService::findProductById($product_id);
            ProductService::updateProductFromAttributesArray($product, $clean_attributes);
        }

        $product->updateTaxAmount();
        $product->save();

        $current_p_structure_attrs = PStructureService::getAllPAttrsByProductsIds([$product->id]);
        $current_p_structure_value_ids = array_map(function (PAttr $p_attr) {
            return $p_attr->p_structure_attr_value_id;
        }, $current_p_structure_attrs->all());

        $key_ids = [];
        foreach ($p_structure_attributes as $p_structure_attribute) {
            /** @var PStructureAttrKey $key */
            $key = $p_structure_attribute["key"];
            $key_ids[] = $key->id;
            foreach ($p_structure_attribute["values"] as $value) {
                $p_structure_value_ids[] = $value->id;

                if (!in_array($value->id, $current_p_structure_value_ids)) {
                    ProductService::attachAttributeToProduct($product, $key, $value);
                }
            }
        }

        foreach ($current_p_structure_attrs as $current_p_structure_attr) {
            if (!in_array($current_p_structure_attr->p_structure_attr_value_id, $p_structure_value_ids)) {
                ProductService::detachAttributeFromProduct($product, $current_p_structure_attr->key, $current_p_structure_attr->value);
            }
        }

        $p_structure->attributeKeys()->sync($key_ids);

        return $product;
    }
}
