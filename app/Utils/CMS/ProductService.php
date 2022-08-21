<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 5/12/18
 * Time: 6:12 PM
 */

namespace App\Utils\CMS;


use App\Models\Color;
use App\Models\Directory;
use App\Models\Product;
use App\Models\PStructureAttrKey;
use App\Models\Setting;
use App\Utils\CMS\Enums\CMSSettingKey;
use Exception;
use Illuminate\Support\Facades\DB;
use stdClass;

class ProductService
{
    public static function getTollPercentage()
    {
        try {
            return floatval(Setting::getCMSRecord(CMSSettingKey::TOLL_PERCENTAGE)->value);
        } catch (Exception $e) {
            return 3.0;
        }
    }

    public static function getTaxPercentage()
    {
        try {
            return floatval(Setting::getCMSRecord(CMSSettingKey::TAX_PERCENTAGE)->value);
        } catch (Exception $e) {
            return 6.0;
        }
    }

    public static function getAllExtrasPercentage()
    {
        return static::getTaxPercentage() + static::getTollPercentage();
    }

    /**
     * @param integer $amount
     * @param int $count
     * @return int
     */
    public static function getTollAmount($amount, $count = 1)
    {
        return intval(($amount * $count) * static::getTollPercentage() / 100);
    }

    /**
     * @param integer $amount
     * @param int $count
     * @return int
     */
    public static function getTaxAmount($amount, $count = 1)
    {
        return intval(($amount * $count) * static::getTaxPercentage() / 100);
    }

    /**
     * @param integer $amount
     * @return integer
     */
    public static function getTollAndTaxAmount($amount)
    {
        return static::getTollAmount($amount) + static::getTaxAmount($amount);
    }

    public static function getPureAmount($amount)
    {
        if ($amount === 0)
            return $amount;

        $taxAndToll = static::getTaxPercentage() + static::getTollPercentage();
        return intval($amount * 100 / (100 + $taxAndToll)) + 1;
    }

    /**
     * @param integer $standardAmount
     * @param integer $count
     * @return stdClass
     */
    public static function reverseCalculateTaxAndToll($standardAmount, $count = 1)
    {
        $result = new stdClass();
        $result->price = static::getPureAmount($standardAmount);
        $result->tax = static::getTaxAmount($result->price, $count);
        $result->toll = intval($standardAmount - ($result->price + $result->tax));

        return $result;
    }

    /**
     * @param integer $amount
     * @param integer $count
     * @return stdClass
     */
    public static function calculateTaxAndToll($amount, $count = 1)
    {
        $result = new stdClass();
        $result->price = $amount;
        $result->tax = static::getTaxAmount($result->price, $count);
        $result->toll = static::getTollAmount($result->price, $count);

        return $result;
    }

    public static function getPriceRatio()
    {
        return doubleval(env('SITE_PRICE_RATIO', '1.0'));
    }

    private static function buildDirectoryGraph($directories, $parent_id = 0): array
    {
        $directories_count = count($directories);
        if ($directories_count == 0)
            return [];

        if ($parent_id === 0) {
            $roots = [];
            $root_ids = [];
            for ($i = 0; $i < count($directories) and !in_array($directories->get($i)->directory_id, $root_ids); $i++) {
                $root_directory = $directories->get($i);
                $root_directory->child_nodes = static::buildDirectoryGraph($directories, $root_directory->id);
                $roots[] = $root_directory;
                $root_ids[] = $root_directory->id;
            }

            return $roots;
        } else {
            $children = [];

            for ($i = 0; $i < count($directories); $i++) {
                $directory = $directories->get($i);
                if ($directory->directory_id === $parent_id) {
                    $directory->child_nodes = static::buildDirectoryGraph($directories, $directory->id);
                    $children[] = $directory;
                }
            }

            return $children;
        }
    }

    /**
     * @param integer[] $productsIds
     * @return array
     */
    public static function getFilterData($productsIds)
    {
        $priceRange = [];
        if (count(is_countable($productsIds) ? $productsIds : []) == 0) {
            $priceRange["min"] = 0;
            $priceRange["max"] = 0;
            $keys = [];
            $colors = [];
            $directories = [];
        } else {
            $priceRange["min"] = Product::whereIn('id', $productsIds)->min('latest_price');
            $priceRange["max"] = Product::whereIn('id', $productsIds)->max('latest_price');

            $keys = PStructureAttrKey::getFilterBladeKeys($productsIds);
            $directories = static::buildDirectoryGraph(Directory::join("directory_product", function ($join) use ($productsIds) {
                $join->on("directories.id", "=", "directory_product.directory_id")
                    ->whereIn("product_id", $productsIds);
            })->groupBy("id")->orderBy("repeat_count", "DESC")
                ->selectRaw(DB::raw("directories.*, count(id) as repeat_count"))->get());

            $colors = Color::whereHas('products', function ($query) use ($productsIds) {
                $query->whereIn('product_id', $productsIds);
            })->get();
        }

        if ($priceRange["min"] == $priceRange["max"])
            $priceRange["max"] += 10000;
        return [
            "priceRange" => $priceRange,
            "keys" => $keys,
            "colors" => $colors,
            "directories" => $directories
        ];
    }
}
