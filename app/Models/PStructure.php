<?php

namespace App\Models;


use App\Utils\Translation\Traits\Translatable;

/**
 *
 * @property integer id
 * @property string title
 * @property string blade_name
 * @property boolean is_shippable
 *
 * @property Product[] products
 * @property PStructureAttrKey[] attributeKeys;
 *
 * Class PStructure
 * @package App\Models
 */
class PStructure extends BaseModel
{
    use Translatable;

    protected $table = 'p_structures';

    protected $fillable = [
        'title', 'blade_name', 'is_shippable'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'title'];

    protected static array $TRANSLATABLE_FIELDS = [
        'title' => ['string', 'input:text']
    ];

    /*
     * Relations Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany('\\App\\Models\\Product', 'p_structure_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attributeKeys()
    {
        return $this->belongsToMany('\\App\\Models\\PStructureAttrKey', 'p_structure_attrs',
            'p_structure_id', 'p_structure_attr_key_id');
    }

    /**
     * @return PStructureAttrKey|null
     */
    public function getSortableKey(): ?PStructureAttrKey
    {
        $sortable_attribute_key = null;
        foreach ($this->attributeKeys as $attributeKey)
            if ($attributeKey->is_sortable) {
                $sortable_attribute_key = $attributeKey;
                break;
            }
        return $sortable_attribute_key;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
