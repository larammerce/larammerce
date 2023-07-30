<?php

namespace App\Models;

use App\Interfaces\TagInterface as TaggableContract;
use App\Jobs\UpdateProductsStructureSortScore;
use App\Traits\Taggable;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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

    protected $casts = [
        'is_model_option' => "bool",
        'is_sortable' => "bool"
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'title', 'priority'];
    protected static array $SEARCHABLE_FIELDS = ["id", "title"];
    protected static array $TRANSLATABLE_FIELDS = [
        'title' => ['string', 'input:text']
    ];

    /*
     * Relations Methods
     */
    public function productStructures(): BelongsToMany {
        return $this->belongsToMany(PStructure::class, 'p_structure_attrs',
            'p_structure_attr_key_id', 'p_structure_id');
    }

    public function values(): HasMany {
        return $this->hasMany(PStructureAttrValue::class, 'p_structure_attr_key_id');
    }

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'p_attr_assignments',
            'p_structure_attr_key_id', 'product_id');
    }

    public function attributes(): HasMany {
        return $this->hasMany(PAttr::class, 'p_structure_attr_key_id');
    }


    public function getText(): string {
        return $this->title;
    }

    public function getValue(): int {
        return $this->id;
    }

    public function setIsSortableAttribute($value): void {
        if ($value != ($this->attributes["is_sortable"] ?? null)) {
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

    /**
     * @param array $product_ids
     * @return Collection|PStructureAttrValue[]
     */
    private static function getFilterBladeValues(array $product_ids): Collection|array {
        return PStructureAttrValue::join("p_attr_assignments", function ($join) use ($product_ids) {
            $join->on("p_attr_assignments.p_structure_attr_value_id", "=", "p_structure_attr_values.id")
                ->whereIn("p_attr_assignments.product_id", $product_ids);
        })
            ->orderBy("priority", "ASC")->groupBy("p_structure_attr_values.id")
            ->selectRaw(DB::raw("p_structure_attr_values.*"))->get();
    }

    public static function getFilterBladeKeys($product_ids) {
        $keys = [];
        PAttr::whereIn("product_id", $product_ids)->with("value");

        foreach (static::getFilterBladeValues($product_ids) as $value) {
            if (!isset($keys[$value->p_structure_attr_key_id])) {
                $keys[$value->p_structure_attr_key_id] = PStructureAttrKey::find($value->p_structure_attr_key_id);
            }

            if (!$keys[$value->p_structure_attr_key_id]->relationLoaded("values")) {
                $keys[$value->p_structure_attr_key_id]->setRelation("values", new Collection());
            }
            $keys[$value->p_structure_attr_key_id]->values->push($value);
        }

        foreach ($keys as $key_id => $key) {
            if (count($key->values) === 1) {
                unset($keys[$key_id]);
            }
        }

        usort($keys, function ($a, $b) {
            if ($a->priority == $b->priority)
                return strcmp($a->title, $b->title);
            if ($a->priority < $b->priority)
                return -1;
            return 1;
        });

        return $keys;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return '';
    }
}
