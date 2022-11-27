<?php

namespace App\Services\PStructure;

use App\Models\PAttr;
use App\Models\PStructureAttrKey;
use App\Models\PStructureAttrValue;
use Illuminate\Support\Collection;

class PStructureService {
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
    public static function getAllPStructureAttrKeysById(array|Collection $p_s_attribute_key_ids): Collection|array {
        return PStructureAttrKey::whereIn("id", $p_s_attribute_key_ids)->orderBy("id", "ASC")->get();
    }


    /**
     * @param int[]|Collection $p_s_attribute_value_ids
     * @return PStructureAttrValue[]|Collection
     */
    public static function getAllPStructureAttrValuesById(array|Collection $p_s_attribute_value_ids): Collection|array {
        return PStructureAttrValue::whereIn("id", $p_s_attribute_value_ids)->orderBy("id", "ASC")->get();
    }
}