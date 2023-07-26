<?php

namespace App\Models;

use App\Interfaces\CMSExposedNodeInterface as FileAbstractionContract;
use App\Interfaces\HashInterface;
use App\Interfaces\ImageOwnerInterface;
use App\Interfaces\PublishScheduleInterface;
use App\Interfaces\RateOwnerInterface as RateableContract;
use App\Interfaces\SeoSubjectInterface as SeoableContract;
use App\Interfaces\ShareSubjectInterface;
use App\Traits\Fileable;
use App\Traits\Rateable;
use App\Traits\Seoable;
use App\Utils\Common\ImageService;
use App\Utils\Translation\Traits\Translatable;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
 * @property mixed seo_title
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
    FileAbstractionContract, ShareSubjectInterface, PublishScheduleInterface, ImageOwnerInterface,
    RateableContract, SeoableContract, HashInterface {
    use Rateable, Seoable, Fileable, Translatable;

    protected $table = 'articles';
    public $timestamps = true;

    protected $fillable = [
        'directory_id', 'system_user_id', 'title', 'short_content', 'full_text', 'source', 'average_rating',
        'rates_count', 'seo_keywords', 'seo_title', 'seo_description', 'content_type', 'is_suggested',

        //these are not table fields, these are form sections that role permission system works with
        'tags', 'main_image',
    ];

    static protected array $SORTABLE_FIELDS = ['id', 'title', 'created_at'];
    static protected array $SEARCHABLE_FIELDS = ['seo_keywords', 'title', 'short_content'];
    static protected int $FRONT_PAGINATION_COUNT = 12;
    protected static array $TRANSLATABLE_FIELDS = [
        "title" => ["string", "input:text"],
        "short_content" => ["text", "textarea:normal"],
        "source" => ["string", "input:text"],
        "seo_title" => ["text", "textarea:normal"],
        "seo_keywords" => ["text", "textarea:normal"],
        "seo_description" => ["text", "textarea:normal"],
        "full_text" => ["mediumText", "textarea:rich"],
    ];

    /*
     * Relation Methods
     */

    public function directory(): BelongsTo {
        return $this->belongsTo(Directory::class, 'directory_id');
    }

    public function directories(): BelongsToMany {
        return $this->belongsToMany(Directory::class, 'article_directory',
            'article_id', 'directory_id');
    }

    public function tags(): MorphToMany {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function author(): BelongsTo {
        return $this->belongsTo(SystemUser::class, 'system_user_id');
    }

    public function scopeLatest(Builder $query): Builder {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeExcept(Builder $query, int $id): Builder {
        return $query->where('id', '!=', $id);
    }

    public function scopeFrom(Builder $query, $type): Builder {
        $contentType = 0;
        foreach (get_article_types() as $key => $value)
            if ($type == $value['title'])
                $contentType = $key;
        return $query->where('content_type', $contentType);
    }

    public function scopeSuggested(Builder $query): Builder {
        return $query->where('is_suggested', true);
    }

    public function delete(): ?bool {
        $this->rates()->delete();
        $this->tags()->detach();
        $this->review()->delete();

        return parent::delete();
    }

    public function hasImage(): bool {
        return $this->image_path != null;
    }

    public function getImagePath(): string {
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

    public function getDefaultImagePath(): string {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName(): string {
        return 'blog';
    }

    public static function getFrontPaginationCount(): int {
        return self::$FRONT_PAGINATION_COUNT;
    }

    public function parentField(): string {
        return 'directory_id';
    }

    public function getName(): string {
        return $this->title;
    }

    public function getAdminUrl(): string {
        return route('admin.article.show', $this);
    }

    public function getFrontUrl(): string {
        return lm_route('public.view-blog', $this) . '/' . url_encode($this->title);
    }

    public function getSearchUrl(): string {
        return '';
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getSeoTitle(): string {
        if ($this->seo_title !== null and strlen($this->seo_title) > 0)
            return $this->seo_title;
        return $this->title . " - " . $this->directory->title;
    }


    public function getSeoDescription(): string {
        return $this->seo_description;
    }

    public function getSeoKeywords(): string {
        return $this->seo_keywords;
    }

    public function attachFileTo(?Directory $dest): void {
        $this->directory_id = $dest?->id;
        $this->save();
        $dest?->attachLeafFiles($this->id);
    }

    /**
     * @throws Exception
     */
    public function detachFile($dest = null) {
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

    public function cloneFile() {
        $newArticle = $this->replicate();
        $newArticle->push();
        $newArticle->createReview();
        return $newArticle;
    }

    public function generateNewUrls($dest) {
        // TODO: Implement generateNewUrls() method.
        $this->save();
    }

    public function getHash(): string {
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

    public function isImageLocal(): bool {
        return true;
    }
}
