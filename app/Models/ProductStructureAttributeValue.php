<?php

namespace App\Models;

use App\Models\Interfaces\ImageContract;
use App\Models\Interfaces\TagContract as TaggableContract;
use App\Models\Traits\Taggable;
use App\Utils\Common\ImageService;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string name
 * @property integer p_structure_attr_key_id
 * @property string image_path
 * @property string image_alias
 * @property integer priority
 *
 * @property ProductStructureAttributeKey key
 * @property ProductAttribute[] attributes
 * @property Product[] products
 *
 * Class ProductStructureAttributeValue
 * @package App\Models
 */
class ProductStructureAttributeValue extends BaseModel implements TaggableContract, ImageContract
{
    use Taggable;

    protected $table = 'p_structure_attr_values';

    protected $fillable = [
        'name', 'en_name', 'p_structure_attr_key_id', 'image_path', 'image_alias', 'priority'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'name'];


    /*
     * Relations Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function key()
    {
        return $this->belongsTo('\\App\\Models\\ProductStructureAttributeKey', 'p_structure_attr_key_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('\\App\\Models\\Product', 'p_attr_assignments',
            'p_structure_attr_value_id', 'product_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'p_structure_attr_value_id');
    }

    public function getText(): string
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    public function hasImage()
    {
        return $this->image_path != null;
    }

    public function getImagePath()
    {
        return $this->image_path;
    }

    public function setImagePath()
    {
        $tmpImage = ImageService::saveImage($this->getImageCategoryName());
        $this->image_path = $tmpImage->destinationPath . '/' . $tmpImage->name;
        $this->save();
    }

    public function removeImage()
    {
        $this->image_path = null;
        $this->save();
    }

    public function getDefaultImagePath()
    {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName()
    {
        return 'p_structure_attr_value';
    }

    public function isImageLocal()
    {
        return true;
    }

    public function save(array $options = []): bool
    {
        $is_priority_changed = $this->isDirty("priority");
        $parent_result = parent::save($options);

        if ($is_priority_changed and $this->key->is_sortable) {
            foreach ($this->products as $related_product) {
                $related_product->buildStructureSortScore($this->key);
            }
        }

        return $parent_result;
    }
}
