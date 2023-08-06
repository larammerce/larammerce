<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @property integer id
 * @property integer product_id
 * @property boolean is_main
 * @property boolean is_secondary
 * @property string caption
 * @property string path
 * @property string real_name
 * @property string extension
 * @property string link
 * @property integer priority
 *
 * @property Product product
 *
 * Class ProductImage
 * @package App\Models
 */
class ProductImage extends BaseModel implements ImageOwnerInterface {
    use Translatable;

    protected $table = "product_images";

    protected $fillable = [
        "product_id", "is_main", "caption", "path", "real_name",
        "is_secondary", "extension", "link", "priority"
    ];

    public $timestamps = false;

    protected static array $TRANSLATABLE_FIELDS = [
        "caption" => ["string", "input:text"]
    ];

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class, "product_id", "id");
    }

    public function scopeMain($query) {
        return $query->where("is_main", true);
    }

    public function scopeSecondary($query) {
        return $query->where("is_secondary", true);
    }

    public function scopeExtension($query, array|string $extensions, array $except = []): mixed {
        if (!is_array($extensions))
            $extensions = [$extensions];
        if (sizeof($except) > 0)
            return $query->whereIn("extension", $extensions)
                ->whereNotIn("extension", $except);
        return $query->whereIn("extension", $extensions);
    }

    public function scopeNotExtension($query, array|string $extensions): mixed {
        if (!is_array($extensions))
            $extensions = [$extensions];
        return $query->whereNotIn("extension", $extensions);
    }

    public function hasImage(): bool {
        if (isset($this))
            return true;
        return false;
    }

    public function getImagePath(): string {
        return $this->path . "/" . $this->real_name;
    }

    public function setImagePath() {
        // the process is in the store method in controller
    }

    public function removeImage() {
        $this->delete();
    }

    public function getDefaultImagePath(): string {
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getImageCategoryName(): string {
        return "product";
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return "";
    }

    public function isImageLocal(): bool {
        return true;
    }
}
