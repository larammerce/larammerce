<?php


namespace App\Models\WP;

use App\Models\BaseModel;
use App\Models\Interfaces\ImageContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WPPost extends BaseModel implements ImageContract
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

    public function getTitleAttribute()
    {
        return $this->post_title;
    }

    public function getCreatedAtAttribute()
    {
        return $this->post_date;
    }

    public function getShortContentAttribute()
    {
        return $this->post_excerpt;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('\\App\\Models\\WP\\WPPost', 'post_parent');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany('App\\Models\\WP\\WPPost', 'post_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metas()
    {
        return $this->hasMany('App\\Models\\WP\\WPPostMeta', 'post_id');
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

    public function getCategories()
    {
        $categories = DB::connection("wp_connection")->table("wp_terms")->select("name", "slug")
            ->join("wp_term_taxonomy", "wp_terms.term_id", "=", "wp_term_taxonomy.term_id")
            ->join("wp_term_relationships as wpr", "wpr.term_taxonomy_id", "=", "wp_term_taxonomy.term_taxonomy_id")
            ->where("taxonomy", "category")->where("wpr.object_id", $this->ID)->get();

        return $categories;
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLatest(Builder $query)
    {
        return $query->orderBy('ID', 'DESC');
    }

    public function getFrontUrl()
    {
        return $this->guid;
    }

    /**
     * TODO: this method should be changed to search actions, array of strings(url of actions)
     *
     * @return string
     */
    public function getSearchUrl(): string
    {
        return "";
    }

    public function hasImage()
    {
        return $this->getThumbnailId() != null;
    }

    public function getImagePath()
    {
        return $this->getThumbnailPath();
    }

    public function setImagePath()
    {
        return true;
    }

    public function removeImage()
    {
        return true;
    }

    public function getDefaultImagePath()
    {
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getImageCategoryName()
    {
        return "not_categorized";
    }

    public function isImageLocal()
    {
        return false;
    }
}
