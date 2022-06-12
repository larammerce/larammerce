<?php

namespace App\Models;

use App\Models\Interfaces\FileContract;
use App\Models\Interfaces\FileContract as FileAbstractionContract;
use App\Models\Interfaces\HashContract;
use App\Models\Interfaces\ImageContract;
use App\Models\Interfaces\PublishScheduleContract;
use App\Models\Interfaces\RateContract as RateableContract;
use App\Models\Interfaces\SeoContract as SeoableContract;
use App\Models\Interfaces\ShareContract;
use App\Models\Traits\Fileable;
use App\Models\Traits\Rateable;
use App\Models\Traits\Seoable;
use App\Utils\Common\ImageService;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer id
 * @property integer directory_id
 * @property integer system_user_id
 * @property string title
 * @property string short_content
 * @property string full_text
 * @property string source
 * @property string image_path
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property float average_rating
 * @property int rates_count
 * @property int content_type
 * @property mixed seo_description
 * @property mixed seo_keywords
 * @property boolean is_suggested
 *
 * @property Directory directory
 * @property Directory[] directories
 * @property Tag[] tags
 * @property SystemUser author
 *
 * Class Article
 * @package App\Models
 */
class Article extends BaseModel implements
    FileAbstractionContract, ShareContract, PublishScheduleContract, ImageContract,
    RateableContract, SeoableContract, HashContract
{
    use Rateable, Seoable, Fileable;

    protected $table = 'articles';
    public $timestamps = true;

    protected $fillable = [
        'directory_id', 'system_user_id', 'title', 'short_content', 'full_text', 'source', 'average_rating',
        'rates_count', 'seo_keywords', 'seo_description', 'content_type', 'is_suggested',

        //these are not table fields, these are form sections that role permission system works with
        'tags', 'main_image',
    ];

    static protected array $SORTABLE_FIELDS = ['id', 'title', 'created_at'];
    static protected array $SEARCHABLE_FIELDS = ['seo_keywords', 'title', 'short_content'];
    static protected int $FRONT_PAGINATION_COUNT = 12;

    /*
     * Relation Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function directory()
    {
        return $this->belongsTo('\\App\\Models\\Directory', 'directory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function directories()
    {
        return $this->belongsToMany('\\App\\Models\\Directory', 'article_directory',
            'article_id', 'directory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function tags()
    {
        return $this->morphToMany('\\App\\Models\\Tag', 'taggable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo('\\App\\Models\\SystemUser', 'system_user_id');
    }

    /*
     * Scope Methods
     */
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeLatest(Builder $query)
    {
        return $query->orderBy('id', 'DESC');
    }

    /**
     * @param Builder $query
     * @param integer $id
     * @return Builder
     */
    public function scopeExcept(Builder $query, $id)
    {
        return $query->where('id', '!=', $id);
    }

    /**
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    public function scopeFrom(Builder $query, $type)
    {
        $contentType = 0;
        foreach (get_article_types() as $key => $value)
            if ($type == $value['title'])
                $contentType = $key;
        return $query->where('content_type', $contentType);
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeSuggested(Builder $query)
    {
        return $query->where('is_suggested', true);
    }

    /*
     * OverWritten Methods
     */
    public function delete()
    {
        $this->rates()->delete();
        $this->tags()->detach();
        $this->review()->delete();

        return parent::delete();
    }


    /*
     * Image Methods
     */

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
        return 'blog';
    }

    /**
     * @return int
     */
    public static function getFrontPaginationCount()
    {
        return self::$FRONT_PAGINATION_COUNT;
    }

    /*
     * Helper Methods
     */
    public function parentField()
    {
        return 'directory_id';
    }

    public function getName()
    {
        return $this->title;
    }

    public function getAdminUrl()
    {
        return route('admin.article.show', $this);
    }

    public function getFrontUrl()
    {
        return route('public.view-blog', $this) . '/' . url_encode($this->title);
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSeoTitle()
    {
        return $this->getTitle() . ' - ' . $this->directory->title;
    }


    public function getSeoDescription()
    {
        return $this->seo_description;
    }

    public function getSeoKeywords()
    {
        return $this->seo_keywords;
    }

    /**
     * @param Directory|null $dest
     */
    public function attachFileTo($dest)
    {
        $this->directory_id = $dest != null ? $dest->id : null;
        $this->save();
        if ($dest != null)
            $dest->attachLeafFiles($this->id);
    }

    /**
     * fill $dest param to detach more efficiently.
     * @param Directory|null $dest
     * @return mixed
     * @throws Exception
     */
    public function detachFile($dest = null)
    {
        if ($this->directory != null) {
            $destParentDirectoriesIds = [];
            if ($dest != null) {
                $destParentDirectories = collect();
                if ($dest->directory_id != null) {
                    $destParentDirectory = $dest->parentDirectory;
                    while ($destParentDirectory != null) {
                        $destParentDirectories->push($destParentDirectory);
                        $destParentDirectory = $destParentDirectory->parentDirectory;
                    }
                }
                $destParentDirectoriesIds = $destParentDirectories->pluck('id')->toArray();
            }

            $parent = $this->directory;
            while ($parent != null and !in_array($parent->id, $destParentDirectoriesIds)) {
                $parent->leafArticles()->detach($this->id);
                $parent = $parent->parentDirectory;
            }
        }
    }

    /**
     * @return Model|FileContract
     */
    public function cloneFile()
    {
        $newArticle = $this->replicate();
        $newArticle->push();
        $newArticle->createReview();
        return $newArticle;
    }

    /**
     * @param $dest
     * @return void
     */
    public function generateNewUrls($dest)
    {
        // TODO: Implement generateNewUrls() method.
        $this->save();
    }

    public function getHash()
    {
        return md5(
            $this->system_user_id . "#",
            $this->title . "#" .
            $this->short_content . "#" .
            $this->full_text . "#" .
            $this->source . "#" .
            ($this->average_rating != null ? $this->average_rating : 0) . "#" .
            ($this->rates_count != null ? $this->rates_count : 0) . "#" .
            $this->seo_keywords . "#" .
            $this->seo_description . "#" .
            $this->content_type . "#" .
            ($this->is_suggested != null ? $this->is_suggested : 0)
        );
    }

    public function isImageLocal()
    {
        return true;
    }
}
