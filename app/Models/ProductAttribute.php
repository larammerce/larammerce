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
 * @property ProductStructureAttributeKey key
 * @property ProductStructureAttributeValue value
 *
 * Class ProductAttribute
 * @package App\Models
 */
class ProductAttribute extends BaseModel
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
        return $this->belongsTo('\\App\\Models\\Product', 'product_id');
    }

    /**
     * @return BelongsTo
     */
    public function value()
    {
        return $this->belongsTo('\\App\\Models\\ProductStructureAttributeValue', 'p_structure_attr_value_id');
    }

    /**
     * @return BelongsTo
     */
    public function key()
    {
        return $this->belongsTo('\\App\\Models\\ProductStructureAttributeKey', 'p_structure_attr_key_id');
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
        try{
            foreach ($products as $product) {
                foreach ($product->attributeValues as $attributeValue) {
                    if ($attributeValue->p_structure_attr_key_id == $keyId)
                        $attributeValue->delete();
                }
                if (isset($product) and $product != null)
                    ProductAttribute::where('product_id', '=', $product->id)
                        ->where('p_structure_attr_key_id', '=', $keyId)
                        ->delete();
            }
        }catch (\Exception $exception){
            Log::error('productAttribute detach key failed : '.$exception->getMessage());
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
        $optionKeys = [];

        if (config('cms.general.site.model_options')) {
            $productModelIds = Product::models($product, false)
                ->orderBy('id', 'DESC')->where('color_code', '=', $product->color_code)
                ->pluck('id')->toArray();

            if(count($productModelIds) > 1){
                $pAttributesTmp = DB::table(DB::raw('p_attr_assignments as paa1'))
                    ->select(DB::raw("*, count(paa1.p_structure_attr_value_id) as paavc1"))
                    ->whereIn('paa1.product_id', $productModelIds)
                    ->join('p_structure_attr_keys as psak1', 'paa1.p_structure_attr_key_id', '=', 'psak1.id')
                    ->where('psak1.is_model_option', '=', true)
                    ->groupBy('paa1.p_structure_attr_value_id')
                    ->having('paavc1', '=', 1)
                    ->join('p_structure_attr_values as psav1', 'paa1.p_structure_attr_value_id', '=', 'psav1.id')->get();

                $pAttributesCounts = [];
                foreach ($pAttributesTmp as $pAttribute) {
                    if (!isset($pAttributesCounts[$pAttribute->p_structure_attr_key_id]))
                        $pAttributesCounts[$pAttribute->p_structure_attr_key_id] = 1;
                    else
                        $pAttributesCounts[$pAttribute->p_structure_attr_key_id] += 1;

                    if (!isset($modelOptions->items[$pAttribute->product_id])) {
                        $modelOptions->items[$pAttribute->product_id] = $pAttribute;
                        $modelOptions->items[$pAttribute->product_id]->name = [$pAttribute->name];
                    } else {
                        $modelOptions->items[$pAttribute->product_id]->name[] = $pAttribute->name;
                    }
                }

                foreach ($pAttributesCounts as $key => $count) {
                    if ($count != count($productModelIds)) {
                        unset($pAttributesCounts[$key]);
                    }
                }

                $optionKeys = array_keys($pAttributesCounts);
                $modelOptions->title = count($optionKeys) == 1 ? $pAttributesTmp[0]->title :
                    Lang::get("ecommerce.product.other_models");
            }
        }

        $pAttributesTmp = DB::table(DB::raw('p_attr_assignments as paa1'))->select("*")
            ->where('paa1.product_id', '=', $product->id)
            ->whereNotIn('paa1.p_structure_attr_key_id', $optionKeys)
            ->join('p_structure_attr_keys as psak1', 'paa1.p_structure_attr_key_id', '=', 'psak1.id')
            ->join('p_structure_attr_values as psav1', 'paa1.p_structure_attr_value_id', '=', 'psav1.id')
            ->orderBy('paa1.p_structure_attr_key_id', 'ASC')
            ->get();

        $attributes = [];
        foreach ($pAttributesTmp as $pAttr){
            if(isset($attributes[$pAttr->p_structure_attr_key_id])){
                $attributes[$pAttr->p_structure_attr_key_id]->values[] = $pAttr;
            }else{
                $attributes[$pAttr->p_structure_attr_key_id] = $pAttr;
                $attributes[$pAttr->p_structure_attr_key_id]->values = [$pAttr];
            }
        }

        return [
            "modelOptions" => $modelOptions,
            "attributes" => $attributes
        ];
    }
}
