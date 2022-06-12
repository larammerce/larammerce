<?php

namespace App\Models;


/**
 *
 * @property integer id
 * @property string title
 * @property string blade_name
 * @property boolean is_shippable
 *
 * @property Product[] products
 * @property ProductStructureAttributeKey[] attributeKeys;
 *
 * Class ProductStructure
 * @package App\Models
 */
class ProductStructure extends BaseModel
{
    protected $table = 'p_structures';

    protected $fillable = [
        'title', 'blade_name', 'is_shippable'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'title'];


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
        return $this->belongsToMany('\\App\\Models\\ProductStructureAttributeKey', 'p_structure_attrs',
            'p_structure_id', 'p_structure_attr_key_id');
    }

    /**
     * @return ProductStructureAttributeKey|null
     */
    public function getSortableKey(): ?ProductStructureAttributeKey
    {
        $sortable_attribute_key = null;
        foreach ($this->attributeKeys as $attributeKey)
            if ($attributeKey->is_sortable){
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
