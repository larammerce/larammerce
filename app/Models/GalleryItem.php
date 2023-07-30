<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Utils\CMS\Template\Gallery\GalleryItemField;
use App\Utils\Common\ImageService;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property string data
 * @property integer gallery_id
 * @property string image_path
 * @property bool is_active
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property Gallery gallery
 *
 * Class GalleryItem
 * @package App\Models
 */
class GalleryItem extends BaseModel implements ImageOwnerInterface
{
    protected $table = "gallery_items";
    protected $hidden = ["data"];
    protected $appends = ["fields"];
    protected $fillable = [
        "data", "gallery_id", "image_path", "priority", "is_active"
    ];

    protected static array $SORTABLE_FIELDS = [
        "id", "priority", "is_active"
    ];

    private $fields;

    public function __construct(array $attributes = [])
    {
        $this->fields = [];
        parent::__construct($attributes);
    }

    public function getFieldsAttribute()
    {
        return $this->getFields();
    }

    private function loadFields()
    {
        $result = true;
        if (count(is_countable($this->fields)?$this->fields :[]) == 0) {
            if ($this->data != null and strlen($this->data) > 0) {
                try {
                    $this->fields = unserialize($this->data);
                } catch (Exception $e) {
                    $this->fields = [];
                    $result = false;
                }
            } else {
                $this->fields = [];
                $result = false;
            }
        }
        return $result;
    }

    public function getField($id)
    {
        if ($this->loadFields()) {
            if (key_exists($id, $this->fields)) {
                return $this->fields[$id];
            }
        }
        return new GalleryItemField($id, "NULL");
    }

    public function getFields()
    {
        if ($this->loadFields())
            return $this->fields;
        return [];
    }

    public function setFields($galleryFields)
    {
        $this->fields = $galleryFields;
        $this->data = serialize($this->fields);
    }

    public function mergeFields($galleryFields)
    {
        $this->loadFields();
        foreach ($galleryFields as $id => $content) {
            if (is_null($content->getContent())  and key_exists($id, $this->fields)) {
                $galleryFields[$id] = $this->fields[$id];
            }
        }
        $this->setFields($galleryFields);
    }

    /**
     * @param array $options
     * @return bool|
     */
    public function save(array $options = [])
    {
        $gallery = null;
        if (request()->has("gallery_id")) {
            $gallery = Gallery::find(request()->get("gallery_id"));
        } else {
            if (isset($this->gallery_id)) {
                $gallery = $this->gallery;
            }
        }
        if ($gallery != null) {
            $fields = [];
            foreach ($gallery->getGalleryFields() as $id => $galleryField) {
                $newField = new GalleryItemField($id, request()->get("data__field__" . $id));
                $fields[$id] = $newField;
            }
            $this->mergeFields($fields);
            return parent::save($options);
        }
        return false;
    }

    /*
     * Relation Methods
     */

    /**
     * @return BelongsTo
     */
    public function gallery()
    {
        return $this->belongsTo("\\App\\Models\\Gallery", "gallery_id");
    }

    public function scopeVisible(Builder $query)
    {
        $query->where("is_active", true);
    }

    /*
     * Accessor Methods
     */
    public function getDataObjAttribute()
    {
        return json_decode($this->data);
    }


    /*
     * Image Methods
     */
    public function hasImage()
    {
        return isset($this->image_path);
    }

    public function getImagePath()
    {
        return $this->image_path;
    }

    public function setImagePath()
    {
        $tmpImage = ImageService::saveImage($this->getImageCategoryName());
        $this->image_path = $tmpImage->destinationPath . "/" . $tmpImage->name;
        $this->save();
    }

    public function removeImage()
    {
        $this->image_path = null;
        $this->save();
    }

    public function getDefaultImagePath()
    {
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getImageCategoryName()
    {
        return "not_categorized";
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return "";
    }

    public function isImageLocal()
    {
        return true;
    }
}
