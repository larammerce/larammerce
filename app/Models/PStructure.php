<?php

namespace App\Models;


use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'p_structure_id');
    }

    public function attributeKeys(): BelongsToMany
    {
        return $this->belongsToMany(PStructureAttrKey::class, 'p_structure_attrs',
            'p_structure_id', 'p_structure_attr_key_id');
    }

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

    public function getSearchUrl(): string
    {
        return '';
    }
}
