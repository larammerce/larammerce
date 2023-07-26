<?php

namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use App\Utils\Common\ImageService;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\View;


class Modal extends BaseModel implements ImageOwnerInterface
{
    use Translatable;

    protected $table = 'modals';

    protected $fillable = [
        'title', 'text', 'repeat_count', 'template', 'size_class', 'buttons'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'created_at'];
    protected static array $roleFillable = [
        "super_user" => ["*"],
        "cms_manager" => ["*"]
    ];
    private array $cached_attributes = [];

    protected static array $TRANSLATABLE_FIELDS = [
        'title' => ['string', 'input:text'],
        'text' => ['longText', 'textarea:rich']
    ];

    public function getDecodedButtons() {
        if (!isset($this->cached_attributes["buttons_decoded"])) {
            $this->cached_attributes["buttons_decoded"] = json_decode($this->attributes["buttons"]);
        }
        return $this->cached_attributes["buttons_decoded"] ?? [];
    }

    public function routes(): HasMany {
        return $this->hasMany(ModalRoute::class, 'modal_id');
    }

    public function getText() {
        return $this->title;
    }

    public function getValue() {
        return $this->id;
    }

    public function hasImage() {
        return isset($this->image_path);
    }

    public function getImagePath() {
        return $this->image_path;
    }

    public function setImagePath() {
        $tmpImage = ImageService::saveImage($this->getImageCategoryName());
        $this->image_path = $tmpImage->destinationPath . '/' . $tmpImage->name;
        $this->save();
    }

    public function removeImage() {
        $this->image_path = null;
        $this->save();
    }

    public function getDefaultImagePath() {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName() {
        return 'modal';
    }

    public function isImageLocal() {
        return 'true';
    }

    public function html(): string {
        $template = "admin.templates.modals.custom_modal";
        if (View::exists("public.custom-modal"))
            $template = "public.custom-modal";
        try {
            return h_view($template)->with(["modal" => $this])->render();
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function getSearchUrl(): string {
        return '';
    }
}
