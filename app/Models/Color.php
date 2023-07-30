<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;
use App\Utils\Common\ImageService;
use App\Utils\Translation\Traits\Translatable;

/**
 *
 * @property integer id
 * @property string name
 * @property string hex_code
 * @property string image_path
 * @property string caption
 * @property Product[] products
 *
 * Class Color
 * @package App\Models
 */
class Color extends BaseModel implements TaggableContract, ImageOwnerInterface
{
    use Taggable, Translatable;

    protected $table = 'colors';

    protected $fillable = [
        'name', 'hex_code', 'image_path', 'caption'
    ];

    static protected array $SORTABLE_FIELDS = ['id', 'name', 'hex_code'];

    protected static array $TRANSLATABLE_FIELDS = [
        'name' => ['string', 'input:text'],
        'caption' => ['string', 'input:text']
    ];

    /*
     * Relation Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany('\\App\\Models\\Product', 'product_color',
            'color_id', 'product_id');
    }


    /*
     * Helper Methods
     */

    public function getText()
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
        return 'color';
    }

    public function isImageLocal()
    {
        return true;
    }
}
