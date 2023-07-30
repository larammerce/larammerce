<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;
use App\Utils\Common\ImageService;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property string title
 * @property string color
 * @property string icon
 * @property string image_path
 * @property Carbon created_at
 * @property Carbon updated_at
 * @property bool show_title
 */
class Badge extends BaseModel implements ImageOwnerInterface, TaggableContract
{
    use Taggable, Translatable;

    protected $table = 'badges';
    protected $fillable = [
        'title', 'color', 'icon', 'show_title'
    ];
    protected $casts = [
        "show_title" => "bool"
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'created_at'];
    protected static array $ROLE_PROPERTY_ACCESS = [
        "super_user" => ["*"],
        "cms_manager" => ["*"]
    ];

    protected static array $TRANSLATABLE_FIELDS = [
        'title' => ['string', 'input:text']
    ];

    public function products(): MorphToMany
    {
        return $this->morphedByMany(Product::class, 'badgeable');
    }

    public function directories(): MorphToMany
    {
        return $this->morphedByMany(Directory::class, 'badgeable');
    }

    public function getText(): string
    {
        return $this->title;
    }

    public function getValue(): int
    {
        return $this->id;
    }

    public function hasImage(): bool
    {
        return isset($this->image_path);
    }

    public function getImagePath(): ?string
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

    public function getDefaultImagePath(): string
    {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName(): string
    {
        return 'badge';
    }

    public function isImageLocal(): string
    {
        return 'true';
    }

    public function html(): string
    {
        return view("admin.templates.badge")->with(["badge" => $this])->render();
    }

    public function getSearchUrl(): string
    {
        return '';
    }
}
