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
            return Setting::getCMSRecord(CMSSettingKey::TOLL_PERCENTAGE)->value;
        } catch (Exception $e) {
            return 3;
        }
    }

    public static function getTaxPercentage()
    {
        try {
            return Setting::getCMSRecord(CMSSettingKey::TAX_PERCENTAGE)->value;
        } catch (Exception $e) {
            return 6;
        }
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

    /**
     * @param integer[] $product_ids
     * @return array
     */
    public static function getFilterData($product_ids)
    {
        $price_range = [];
        if (count(is_countable($product_ids) ? $product_ids : []) == 0) {
            $price_range["min"] = 0;
            $price_range["max"] = 0;
            $keys = [];
            $colors = [];
            $directories = [];
        } else {
            $price_range["min"] = Product::whereIn('id', $product_ids)->min('latest_price');
            $price_range["max"] = Product::whereIn('id', $product_ids)->max('latest_price');

            $keys = PStructureAttrKey::getFilterBladeKeys($product_ids);
            $directories = Directory::join("directory_product", function ($join) use ($product_ids) {
                $join->on("directories.id", "=", "directory_product.directory_id")
                    ->whereIn("product_id", $product_ids);
            })->orderBy("priority", "ASC")->get();

            $colors = Color::whereHas('products', function ($query) use ($product_ids) {
                $query->whereIn('product_id', $product_ids);
            })->get();
        }

        if ($price_range["min"] == $price_range["max"])
            $price_range["max"] += 10000;
        return [
            "priceRange" => $price_range,
            "keys" => $keys,
            "colors" => $colors,
            "directories" => $directories
        ];
    }
}
