<?php


namespace App\Models;

use App\Interfaces\ImageOwnerInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @property string $post_title
 * @property Carbon $post_date
 * @property string $post_excerpt
 * @property string $guid
 */
class WPPost extends BaseModel implements ImageOwnerInterface
{
    public $connection = 'wp_connection';
    protected $table = 'wp_posts';
    protected $primaryKey = 'ID';
    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('blog_posts', function (Builder $builder) {
            $builder->where('post_status', 'publish')->where('post_type', 'post');
        });
    }

    public function getDirectoryAttribute(): object
    {
        $categories = $this->getCategories();

        return new class($categories) {
            public $title;
            public $slug;

            public function __construct($categories)
            {
                if (count(is_countable($categories)?$categories :[]) > 0) {
                    $this->slug = $categories[0]->slug;
                    $this->title = $categories[0]->name;
                } else {
                    $this->slug = "";
                    $this->title = "";
                }

            }

            public function getFrontUrl(): string
            {
                return config("wp.address") . "/category/" . $this->slug;
            }
        };
    }

    public function getTitleAttribute(): string {
        return $this->post_title;
    }

    public function getCreatedAtAttribute(): Carbon
    {
        return $this->post_date;
    }

    public function getShortContentAttribute(): string {
        return $this->post_excerpt;
    }

    public function parent(): BelongsTo {
        return $this->belongsTo(WPPost::class, 'post_parent');
    }

    public function children(): HasMany {
        return $this->hasMany(WPPost::class, 'post_parent');
    }

    public function metas(): HasMany {
        return $this->hasMany(WPPostMeta::class, 'post_id');
    }

    private function getThumbnailId()
    {
        return $this->metas()->thumbnailId()->first();
    }

    public function getThumbnailPath(): string
    {
        $post_thumbnail_id = $this->getThumbnailId();
        if ($post_thumbnail_id != null) {
            $post_thumbnail_id = $post_thumbnail_id->meta_value;
            $thumbnail_data = WPPostMeta::where("post_id", $post_thumbnail_id)->where("meta_key", "_wp_attached_file")->first();
            if ($thumbnail_data != null) {
                return config("wp.address") . config("wp.uploads_path") . $thumbnail_data->meta_value;
            }
        }
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getCategories(): Collection {
        return DB::connection("wp_connection")->table("wp_terms")->select("name", "slug")
            ->join("wp_term_taxonomy", "wp_terms.term_id", "=", "wp_term_taxonomy.term_id")
            ->join("wp_term_relationships as wpr", "wpr.term_taxonomy_id", "=", "wp_term_taxonomy.term_taxonomy_id")
            ->where("taxonomy", "category")->where("wpr.object_id", $this->ID)->get();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLatest(Builder $query): Builder {
        return $query->orderBy('ID', 'DESC');
    }

    public function getFrontUrl(): string {
        return $this->guid;
    }

    public function getSearchUrl(): string
    {
        return "";
    }

    public function hasImage(): bool {
        return $this->getThumbnailId() != null;
    }

    public function getImagePath(): string {
        return $this->getThumbnailPath();
    }

    public function setImagePath(): bool {
        return true;
    }

    public function removeImage(): bool {
        return true;
    }

    public function getDefaultImagePath(): string {
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getImageCategoryName(): string {
        return "not_categorized";
    }

    public function isImageLocal(): bool {
        return false;
    }
}
