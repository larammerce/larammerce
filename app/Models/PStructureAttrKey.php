<?php

namespace App\Models;

use App\Jobs\UpdateProductsStructureSortScore;
use App\Models\Interfaces\TagContract as TaggableContract;
use App\Models\Traits\Taggable;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property integer priority
 * @property string title
 * @property integer show_type
 * @property boolean is_model_option
 * @property boolean is_sortable
 *
 * @property PStructure[] productStructures
 * @property PStructureAttrValue[] values
 * @property PAttr[] attributes
 * @property Product[] products
 *
 *
 * Class PStructureAttrKey
 * @package App\Models
 */
class PStructureAttrKey extends BaseModel implements TaggableContract
{
    use Taggable, Translatable;

    protected $table = 'p_structure_attr_keys';

    protected $fillable = [
        'title', 'show_type', 'priority', 'is_model_option', 'is_sortable'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'title', 'priority'];

    protected static array $TRANSLATABLE_FIELDS = [
        'title' => ['string', 'input:text']
    ];

    /*
     * Relations Methods
     */
    public function productStructures(): BelongsToMany
    {
        return $this->belongsToMany(PStructure::class, 'p_structure_attrs',
            'p_structure_attr_key_id', 'p_structure_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(PStructureAttrValue::class, 'p_structure_attr_key_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'p_attr_assignments',
            'p_structure_attr_key_id', 'product_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(PAttr::class, 'p_structure_attr_key_id');
    }


    public function getText(): string
    {
        return $this->title;
    }

    public function getValue(): int
    {
        return $this->id;
    }

    public function setIsSortableAttribute($value): void
    {
        if ($value != $this->attributes["is_sortable"]) {
            $this->attributes["is_sortable"] = $value;
            if ($value) {
                foreach ($this->productStructures as $related_p_structure) {
                    foreach ($related_p_structure->attributeKeys as $related_p_structure_key) {
                        if ($related_p_structure_key->is_sortable)
                            $related_p_structure_key->update(['is_sortable' => false]);
                    }
                }
                $job = new UpdateProductsStructureSortScore($this);
                dispatch($job);
            }
        }
    }

    public static function getFilterBladeKeys($productsIds)
    {
        return PStructureAttrKey::whereHas('attributes', function ($query) use ($productsIds) {
            // to filter keys that are used in selected products
            $query->whereIn('product_id', $productsIds);
        }, '>=', count($productsIds))->with(['values' => function ($query) use ($productsIds) {
            // to filter values that are only used in selected products
            $query->whereHas('attributes', function ($query) use ($productsIds) {
                $query->whereIn('product_id', $productsIds);
            });
        }])->get();
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
