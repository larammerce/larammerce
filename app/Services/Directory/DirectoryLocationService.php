<?php

namespace App\Services\Directory;

use App\Models\DirectoryLocation;
use App\Models\Product;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationModel;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationService;
use Illuminate\Support\Collection;

class DirectoryLocationService {
    private static array $CACHE = [];

    /**
     * @return DirectoryLocation[]|Collection
     */
    public static function getAll(): array|Collection {
        $function_cache_key = "all";
        if (!isset(static::$CACHE[$function_cache_key])) {
            static::$CACHE[$function_cache_key] = DirectoryLocation::with(["city", "state"])->get();
        }
        return static::$CACHE[$function_cache_key];
    }

    public static function getAllMappedByDirectoryId() {
        $function_cache_key = "all_mapped_by_directory_id";
        if (!isset(static::$CACHE[$function_cache_key])) {
            $all_records = static::getAll();
            $result = [];

            foreach ($all_records as $record) {
                if (!isset($result[$record->directory_id])) {
                    $result[$record->directory_id] = [];
                }

                $result[$record->directory_id][] = new CustomerLocationModel($record->state, $record->city);
            }

            static::$CACHE[$function_cache_key] = $result;
        }
        return static::$CACHE[$function_cache_key];
    }

    /**
     * @return int[]
     */
    public static function getLimitedDirectoryIds(): array {
        $function_cache_key = "limited_directory_ids";
        if (!isset(static::$CACHE[$function_cache_key])) {
            static::$CACHE[$function_cache_key] = array_map(function (DirectoryLocation $directory_location) {
                return $directory_location->directory_id;
            }, static::getAll()->all());
        }
        return static::$CACHE[$function_cache_key];
    }

    public static function isProductLocationLimited(Product $product): bool {
        return in_array($product->directory_id, static::getLimitedDirectoryIds());
    }

    /**
     * @param Product $product
     * @return CustomerLocationModel[]
     */
    public static function getProductLocationLimitations(Product $product): array {
        $directory_locations = static::getAllMappedByDirectoryId();
        return $directory_locations[$product->directory_id] ?? [];
    }

    public static function canDeliverProduct(Product $product): bool {
        $selected_location = CustomerLocationService::getRecord();

        if(!static::isProductLocationLimited($product)){
            return true;
        }

        if(is_null($selected_location)){
            return true;
        }

        foreach (static::getProductLocationLimitations($product) as $limitation){
            if($limitation->equals($selected_location)){
                return true;
            }
        }

        return false;
    }
}
