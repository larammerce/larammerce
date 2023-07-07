<?php

namespace App\Services\PStructure;

use App\Exceptions\PStructure\PStructureAttrKeyNotFoundException;
use App\Exceptions\PStructure\PStructureAttrValueNotFoundException;
use App\Models\PAttr;
use App\Models\PStructure;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;
use Illuminate\Support\Collection;

class PStructureService
{
    /**
     * @param int[]|Collection $product_ids
     * @return PAttr[]|Collection
     */
    public static function getAllPAttrsByProductsIds(array|Collection $product_ids): Collection|array {
        return PAttr::whereIn("product_id", $product_ids)
            ->orderBy("product_id", "ASC")
            ->orderBy("p_structure_attr_key_id", "ASC")
            ->get();
    }

    /**
     * @param int[]|Collection $p_s_attribute_key_ids
     * @return PStructureAttrKey[]|Collection
     */
    public static function getAllPStructureAttrKeysByIds(array|Collection $p_s_attribute_key_ids): Collection|array {
        return PStructureAttrKey::whereIn("id", $p_s_attribute_key_ids)->orderBy("id", "ASC")->get();
    }


    /**
     * @param int[]|Collection $p_s_attribute_value_ids
     * @return PStructureAttrValue[]|Collection
     */
    public static function getAllPStructureAttrValuesByIds(array|Collection $p_s_attribute_value_ids): Collection|array {
        return PStructureAttrValue::whereIn("id", $p_s_attribute_value_ids)->orderBy("id", "ASC")->get();
    }

    /**
     * @throws PStructureAttrKeyNotFoundException
     */
    public static function findKeyByTitle(string $title): PStructureAttrKey {
        /**
         * @var PStructureAttrKey $p_structure_attr_key
         */
        $p_structure_attr_key = PStructureAttrKey::where("title", $title)->first();
        if ($p_structure_attr_key == null) {
            throw new PStructureAttrKeyNotFoundException("The p structure key with title `{$title}` does not exist in the database.");
        }
        return $p_structure_attr_key;
    }

    public static function findOrCreateKeyByTitle(string $title, int $key_order): PStructureAttrKey {
        try {
            $key = static::findKeyByTitle($title);
            if ($key->priority != $key_order) {
                $key->update(["priority" => $key_order]);
            }
            return $key;
        } catch (PStructureAttrKeyNotFoundException $e) {
            return static::createKey($title, priority: $key_order);
        }
    }

    public static function createKey(
        string $title,
        int    $show_type = 0,
        int    $priority = 0,
        bool   $is_model_option = false,
        bool   $is_sortable = false
    ): PStructureAttrKey {
        return PStructureAttrKey::create([
            "title" => $title,
            "show_type" => $show_type,
            "priority" => $priority,
            "is_model_option" => $is_model_option,
            "is_sortable" => $is_sortable
        ]);
    }


    /**
     * @throws PStructureAttrValueNotFoundException
     */
    public static function findValueByName(string $name, PStructureAttrKey $key): PStructureAttrValue {
        /**
         * @var PStructureAttrValue $p_structure_attr_value
         */
        $p_structure_attr_value = $key->values()->where("name", $name)->first();
        if ($p_structure_attr_value == null) {
            throw new PStructureAttrValueNotFoundException("The p structure value with title `{$name}` does not exist in the database.");
        }
        return $p_structure_attr_value;
    }

    public static function findOrCreateValueByName(string $name, PStructureAttrKey $key): PStructureAttrValue {
        try {
            return static::findValueByName($name, $key);
        } catch (PStructureAttrValueNotFoundException $e) {
            return static::createValue($name, $key);
        }
    }

    public static function createValue(
        string            $name,
        PStructureAttrKey $key,
        string            $en_name = "",
        string            $image_path = "",
        string            $image_alias = "",
        int               $priority = 0
    ): PStructureAttrValue {
        return PStructureAttrValue::create([
            'name' => $name,
            'en_name' => $en_name,
            'p_structure_attr_key_id' => $key->id,
            'image_path' => $image_path,
            'image_alias' => $image_alias,
            'priority' => $priority
        ]);
    }
}
