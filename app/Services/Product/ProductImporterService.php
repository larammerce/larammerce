<?php

namespace App\Services\Product;

use App\Exceptions\Directory\DirectoryNotFoundException;
use App\Exceptions\Product\ProductNotFoundException;
use App\Models\Product;
use App\Models\PStructure;
use App\Services\Directory\DirectoryService;
use App\Services\PStructure\PStructureService;
use Illuminate\Support\Facades\Schema;

class ProductImporterService {

    private static function getMainAttributesFromDataArray(array $data_array): array {
        $result = [];
        $tmp_product = new Product();

        foreach ($tmp_product->getFillable() as $fillable) {
            if ($data_array[$fillable] == null)
                continue;

            $result[$fillable] = $data_array[$fillable];
        }

        return $result;
    }

    private static function getPStructureAttributesFromDataArray(array $data_array): array {
        $values_title_map = [];
        $keys_title_map = [];
        $result = [];
        $tmp_product = new Product();
        $product_table_columns = Schema::getColumnListing($tmp_product->getTable());


        foreach ($data_array as $key_title => $value_title) {
            if ($value_title == null or $key_title == null)
                continue;

            if (in_array($key_title, $product_table_columns))
                continue;
            if (str_starts_with($key_title, "e:") or str_starts_with($key_title, "E:"))
                continue;

            if (key_exists($key_title, $keys_title_map))
                $key = $keys_title_map[$key_title];
            else {
                $key = PStructureService::findOrCreateKeyByTitle($key_title);
                $keys_title_map[$key_title] = $key;
            }

            $exploded_value_titles = array_map(function (string $title) {
                return trim($title);
            }, explode(",", $value_title));
            $values = [];
            foreach ($exploded_value_titles as $exploded_value_title) {
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
        foreach ($data_array as $key => $value) {
            if ($value == null or $key == null)
                continue;

            if (str_starts_with($key, "e:") or str_starts_with($key, "E:")) {
                $key = str_replace(["e:", "E:"], "", $key);
                $result[] = [
                    "key" => $key,
                    "value" => $value
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
        $product_id = $data_array["id"];
        $directory_id = $data_array["directory_id"];

        $main_attributes = static::getMainAttributesFromDataArray($data_array);
        $p_structure_attributes = static::getPStructureAttributesFromDataArray($data_array);
        $extra_properties = json_encode(static::getExtraPropertiesFromDataArray($data_array));
        $main_attributes["extra_properties"] = $extra_properties;

        if ($product_id == null) {
            $directory = DirectoryService::findDirectoryById($directory_id);
            ProductService::createProductFromAttributesArray($main_attributes, $directory);
        }

        $product = ProductService::findProductById($product_id);
        ProductService::updateProductFromAttributesArray($product, $main_attributes);
    }
}