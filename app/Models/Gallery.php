<?php

namespace App\Models;

use App\Utils\CMS\Template\Gallery\GalleryField;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string identifier
 * @property string fields
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property WebPage webPage
 * @property GalleryItem[] items
 *
 * Class Gallery
 * @package App\Models
 */
class Gallery extends BaseModel
{
    protected $table = "galleries";
    protected $hidden = ["fields"];
    protected $fillable = [
        "identifier", "fields"
    ];

    protected static array $SORTABLE_FIELDS = ["id"];

    /**
     * @var GalleryField[]
     */
    private $galleryFields;

    public function __construct(array $attributes = [])
    {
        $this->galleryFields = [];
        parent::__construct($attributes);
    }

    /*
     * Relation Methods
     */

    /**
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany("\\App\\Models\\GalleryItem", "gallery_id");
    }

    private function loadGalleryFields()
    {
        $result = true;
        if (count(is_countable($this->galleryFields)?$this->galleryFields :[]) == 0) {
            if ($this->fields != null and strlen($this->fields) > 0) {
                try {
                    $this->galleryFields = unserialize($this->fields);
                } catch (Exception $e) {
                    $this->galleryFields = [];
                    $result = false;
                }
            } else {
                $this->galleryFields = [];
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @return GalleryField[]
     */
    public function getGalleryFields()
    {
        if ($this->loadGalleryFields())
            return $this->galleryFields;
        return [];
    }

    /**
     * @param GalleryField[] $galleryFields
     */
    public function setGalleryFields($galleryFields)
    {
        $this->galleryFields = $galleryFields;
        $this->fields = serialize($this->galleryFields);
    }

    public function getGalleryField($id)
    {
        if ($this->loadGalleryFields()) {
            if (key_exists($id, $this->galleryFields)) {
                return $this->galleryFields[$id];
            }
        }
        return null;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return "";
    }
}
