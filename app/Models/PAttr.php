<?php

namespace App\Models;

use App\Models\Enums\DirectoryType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use stdClass;

/**
 * @property integer id
 * @property integer product_id
 * @property integer p_structure_attr_key_id
 * @property integer p_structure_attr_value_id
 *
 * @property Product product
 * @property PStructureAttrKey key
 * @property PStructureAttrValue value
 *
 * Class ProductAttribute
 * @package App\Models
 */
class PAttr extends BaseModel
{
    protected $table = 'p_attr_assignments';

    protected $fillable = [
        'product_id', 'p_structure_attr_key_id', 'p_structure_attr_value_id'
    ];

    public $timestamps = false;


    /*
     * Relations Methods
     */

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * @return BelongsTo
     */
    public function value()
    {
        return $this->belongsTo(PStructureAttrValue::class, 'p_structure_attr_value_id');
    }

    /**
     * @return BelongsTo
     */
    public function key()
    {
        return $this->belongsTo(PStructureAttrKey::class, 'p_structure_attr_key_id');
    }

    /*
     * Helper Function
     */
    public function getUrlHash()
    {
        return $this->key->title . '/' . $this->value->name . '/';
    }

    public function getFilterPath($directory = null)
    {
        if (!$directory)
            $directory = Directory::from(DirectoryType::PRODUCT)->roots()->first();
        return $directory->getFrontUrl() . '#/' . $this->getUrlHash();
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    /**
     * @param Product[] $products
     * @param $keyId
     */
    public static function clean($products, $keyId)
    {
        try {
            foreach ($products as $product) {
                foreach ($product->attributeValues as $attributeValue) {
                    if ($attributeValue->p_structure_attr_key_id == $keyId)
                        $attributeValue->delete();
                }
                if (isset($product) and $product != null)
                    PAttr::where('product_id', '=', $product->id)
                        ->where('p_structure_attr_key_id', '=', $keyId)
                        ->delete();
            }
        } catch (\Exception $exception) {
            Log::error('productAttribute detach key failed : ' . $exception->getMessage());
        }
    }

    /**
     * @param Product $product
     * @return array
     */
    public static function getProductAttributes($product)
    {
        $modelOptions = new stdClass();
        $modelOptions->title = "";
        $modelOptions->items = [];
        $option_keys = [];

        if (config('cms.general.site.model_options')) {
            $productModelIds = Product::models($product, false)
                ->orderBy('id', 'DESC')->where('color_code', '=', $product->color_code)
                ->pluck('id')->toArray();

            if (count($productModelIds) > 1) {
                $tmp_p_attributes = DB::table(DB::raw('p_attr_assignments as paa1'))
                    ->select(DB::raw("*, count(paa1.p_structure_attr_value_id) as paavc1"))
                    ->whereIn('paa1.product_id', $productModelIds)
                    ->join('p_structure_attr_keys as psak1', 'paa1.p_structure_attr_key_id', '=', 'psak1.id')
                    ->where('psak1.is_model_option', '=', true)
                    ->groupBy('paa1.p_structure_attr_value_id')
                    ->having('paavc1', '=', 1)
                    ->join('p_structure_attr_values as psav1', 'paa1.p_structure_attr_value_id', '=', 'psav1.id')->get();

                $p_attributes_counts = [];
                foreach ($tmp_p_attributes as $tmp_p_attribute) {
                    if (!isset($p_attributes_counts[$tmp_p_attribute->p_structure_attr_key_id]))
                        $p_attributes_counts[$tmp_p_attribute->p_structure_attr_key_id] = 1;
                    else
                        $p_attributes_counts[$tmp_p_attribute->p_structure_attr_key_id] += 1;

                    if (!isset($modelOptions->items[$tmp_p_attribute->product_id])) {
                        $modelOptions->items[$tmp_p_attribute->product_id] = $tmp_p_attribute;
                        $modelOptions->items[$tmp_p_attribute->product_id]->name = [$tmp_p_attribute->name];
                    } else {
                        $modelOptions->items[$tmp_p_attribute->product_id]->name[] = $tmp_p_attribute->name;
                    }
                }

                foreach ($p_attributes_counts as $key => $count) {
                    if ($count != count($productModelIds)) {
                        unset($p_attributes_counts[$key]);
                    }
                }

                $option_keys = array_keys($p_attributes_counts);
                $modelOptions->title = count($option_keys) == 1 ? $tmp_p_attributes[0]->title :
                    Lang::get("ecommerce.product.other_models");
            }
        }

        $tmp_p_attributes = static::where('product_id', '=', $product->id)
            ->whereNotIn('p_structure_attr_key_id', $option_keys)
            ->orderBy('p_structure_attr_key_id', 'ASC')
            ->with(["key", "value"])
            ->get();

        $attributes = [];
        foreach ($tmp_p_attributes as $p_attr) {
            if (isset($attributes[$p_attr->p_structure_attr_key_id])) {
                $attributes[$p_attr->p_structure_attr_key_id]->values[] = $p_attr->value;
            } else {
                $attributes[$p_attr->p_structure_attr_key_id] = new stdClass();
                $attributes[$p_attr->p_structure_attr_key_id]->key = $p_attr->key;
                $attributes[$p_attr->p_structure_attr_key_id]->values = [$p_attr->value];
            }
        }

        return compact("attributes", "modelOptions");
    }
}
