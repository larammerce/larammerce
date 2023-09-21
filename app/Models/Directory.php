<?php

namespace App\Models;

use App\Enums\Directory\DirectoryType;
use App\Interfaces\CMSExposedNodeInterface;
use App\Interfaces\HashInterface;
use App\Interfaces\ImageOwnerInterface;
use App\Traits\Badgeable;
use App\Traits\Fileable;
use App\Traits\FullTextSearch;
use App\Utils\CMS\AdminRequestService;
use App\Utils\Common\ImageService;
use App\Utils\Translation\Traits\Translatable;
use Exception;
use Faker\Provider\DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;

/**
 *
 * @property integer id
 * @property string title
 * @property string url_part
 * @property string url_full
 * @property string url_landing
 * @property boolean is_internal_link
 * @property boolean is_anonymously_accessible
 * @property boolean has_web_page
 * @property integer priority
 * @property integer content_type
 * @property integer data_type
 * @property integer directory_id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property boolean show_in_navbar
 * @property boolean show_in_footer
 * @property boolean show_in_app_navbar
 * @property string cover_image_path
 * @property bool is_location_limited
 * @property string description
 * @property integer cmc_id
 * @property boolean force_show_landing
 * @property integer inaccessibility_type
 * @property string notice
 * @property string metadata
 * @property integer depth
 * @property boolean is_hidden
 *
 * @property Directory parentDirectory
 * @property Directory[] directories
 * @property Product[] leafProducts
 * @property Product[] products
 * @property Article[] leafArticles
 * @property Article[] articles
 * @property SystemRole[] systemRoles
 * @property WebPage webPage
 * @property Tag[] tags
 * @property Badge[] badges
 * @property CustomerMetaCategory customerMetaCategory
 *
 * @method static Directory find(integer $id)
 * @method static Builder roots()
 * @method static Builder from(string $type)
 * @method static Builder permitted()
 *
 * Class Directory
 * @package App\Models
 */
class Directory extends BaseModel implements ImageOwnerInterface, HashInterface, CMSExposedNodeInterface {
    use Fileable, Badgeable, Translatable, FullTextSearch;

    protected static array $SORTABLE_FIELDS = ["id", "priority", "title", "created_at"];
    protected static array $SEARCHABLE_FIELDS = ["title", "url_part"];
    protected static ?string $IMPORTANT_SEARCH_FIELD = "title";

    protected $table = "directories";
    public $timestamps = true;
    protected $fillable = [
        "title", "url_part", "url_full", "is_internal_link", "is_anonymously_accessible", "has_web_page", "priority",
        "content_type", "directory_id", "show_in_navbar", "show_in_footer", "cover_image_path", "description",
        "data_type", "show_in_app_navbar", "is_location_limited", "cmc_id", "depth", "is_hidden",
        "badges", "force_show_landing", "inaccessibility_type", "notice", "metadata"
    ];

    protected $casts = [
        "force_show_landing" => "bool",
        "is_hidden" => "bool",
    ];

    protected static array $TRANSLATABLE_FIELDS = [
        "title" => ["string", "input:text"],
        "notice" => ["string", "input:text"],
        "description" => ["text", "textarea:rich"]
    ];

    public function setDirectoryIdAttribute($directory_id) {
        $parent = static::find($directory_id);
        if ($parent == null)
            return;
        $this->depth = $parent->depth + 1;
        $this->attributes["directory_id"] = $directory_id;
    }

    public function getContentTypeTitleAttribute(): string {
        return trans("general.directory.type.{$this->content_type}");
    }

    public function setCmcIdAttribute($value) {
        foreach ($this->directories as $sub_directory) {
            $sub_directory->update(["cmc_id" => $value]);
        }

        foreach ($this->products as $product) {
            $product->update(["cmc_id" => $value]);
        }

        $this->attributes["cmc_id"] = $value;
    }

    public function getUrlLandingAttribute(): string {
        return "$this->url_full/landing";
    }

    public function parentDirectory(): BelongsTo {
        return $this->belongsTo(Directory::class, "directory_id");
    }

    public function directories(): HasMany {
        return $this->hasMany(Directory::class, "directory_id");
    }

    public function leafProducts(): BelongsToMany {
        return $this->belongsToMany(Product::class, "directory_product", "directory_id", "product_id");
    }

    public function products(): HasMany {
        return $this->hasMany(Product::class, "directory_id");
    }

    public function discounts(): BelongsToMany {
        return $this->belongsToMany(DiscountCard::class, "directory_discount_card", "directory_id", "discount_card_id");
    }

    public function leafArticles(): BelongsToMany {
        return $this->belongsToMany(Article::class, "article_directory", "directory_id", "article_id");
    }

    public function articles(): HasMany {
        return $this->hasMany(Article::class, "directory_id");
    }

    public function systemRoles(): BelongsToMany {
        return $this->belongsToMany(SystemRole::class, "directory_system_role", "directory_id", "system_role_id");
    }

    public function webPage(): HasOne {
        return $this->hasOne(WebPage::class, "directory_id");
    }

    public function tags(): MorphToMany {
        return $this->morphToMany(Tag::class, "taggable");
    }

    public function directoryLocations(): HasMany {
        return $this->hasMany(DirectoryLocation::class, "directory_id", "id");
    }

    public function customerMetaCategory(): BelongsTo {
        return $this->belongsTo(CustomerMetaCategory::class, "cmc_id", "id");
    }

    protected static function booted(): void {
        if(!AdminRequestService::isInAdminArea()){
            static::addGlobalScope("visible", function (Builder $builder) {
                $builder->visible();
            });
        }
    }

    public function scopeRoots(Builder $query): Builder {
        return $query->whereNull("directory_id");
    }

    public function scopeFrom(Builder $query, string $type): Builder {
        return $query->where("content_type", $type);
    }

    public function scopePermitted(Builder $builder): Builder {
        $system_user = get_system_user();

        if ($system_user == null or $system_user?->is_super_user)
            return $builder;

        return $builder->whereRaw(DB::raw("not exists (select dsr1.directory_id from directory_system_role as dsr1 where dsr1.directory_id = directories.id)"))
            ->orWhereRaw(DB::raw("exists (select dsr2.directory_id from directory_system_role as dsr2 inner join system_user_system_role as susr1 on dsr2.system_role_id = susr1.system_role_id where dsr2.directory_id = directories.id and susr1.system_user_id={$system_user->id})"));
    }

    public function scopeVisible(Builder $builder): Builder {
        return $builder->where("is_hidden", false);
    }

    public function scopeNavbar(Builder $builder): Builder {
        return $builder->where("show_in_navbar", true);
    }

    public function save(array $options = []): bool {
        $result = parent::save($options);
        if ($result) {
            if ($this->has_web_page) {
                // The $this->webPage had some delays and due to existence of setUrlFull method cal, it would generate 2 webPage
                if (WebPage::where("directory_id", $this->id)->count() == 0) {
                    $webPage = WebPage::create([
                        "directory_id" => $this->id
                    ]);
                    try {
                        $webPage->createReview();
                    } catch (Exception $e) {
                        $webPage->review->update([
                            "needs_review" => false,
                            "edit_count" => 0
                        ]);
                    }
                }
            } else
                if ($this->webPage)
                    $this->webPage->delete();//TODO: handle this.
        }
        return $result;
    }

    public function delete(): ?bool {
        $this->tags()->detach();
        $this->badges()->detach();
        return parent::delete();
    }

    public function updateChildrenUrlFull(): void {
        foreach ($this->directories as $directory) {
            $directory->setUrlFull();
            $directory->updateChildrenUrlFull();
        }
    }

    public function attachLeafFiles($file_ids) {
        if (!is_array($file_ids))
            $file_ids = [$file_ids];
        if ($this->content_type == DirectoryType::PRODUCT)
            $this->leafProducts()->syncWithoutDetaching($file_ids);
        elseif ($this->content_type == DirectoryType::BLOG)
            $this->leafArticles()->syncWithoutDetaching($file_ids);
        if ($this->directory_id != null)
            Directory::find($this->directory_id)->attachLeafFiles($file_ids);
    }

    public function detachLeafFiles($detach_ids, $self_id = null, $dest_parent_directory_ids = []) {
        if (!is_array($detach_ids))
            $detach_ids = [$detach_ids];
        if (!is_array($dest_parent_directory_ids))
            $dest_parent_directory_ids = [$dest_parent_directory_ids];
        $this->detachDetachableLeaves($detach_ids, $self_id, $dest_parent_directory_ids);
    }

    private function detachDetachableLeaves($detachable_ids, $self_id, $dest_parent_directory_ids) {
        $leaf_overlap_ids = [];
        if (count(is_countable($this->directories) ? $this->directories : []) > 1) {
            foreach ($this->directories as $directory) {
                if ($directory->id !== $self_id) {
                    $leaf_ids = [];
                    if ($this->content_type == DirectoryType::PRODUCT)
                        $leaf_ids = $directory->leafProducts()->pluck("id")->toArray();
                    elseif ($this->content_type == DirectoryType::BLOG)
                        $leaf_ids = $directory->leafArticles()->pluck("id")->toArray();

                    foreach ($leaf_ids as $leaf_id)
                        if (!in_array($leaf_id, $leaf_overlap_ids) and in_array($leaf_id, $detachable_ids))
                            $leaf_overlap_ids[] = $leaf_id;
                }
            }
        }
        $detachable_ids = array_diff($detachable_ids, $leaf_overlap_ids);
        if (sizeof($detachable_ids) > 0)
            if ($this->content_type == DirectoryType::PRODUCT)
                $this->leafProducts()->detach($detachable_ids);
            elseif ($this->content_type == DirectoryType::BLOG)
                $this->leafArticles()->detach($detachable_ids);

        if ($this->directory_id != null and !in_array($this->directory_id, $dest_parent_directory_ids))
            $this->parentDirectory->detachDetachableLeaves($detachable_ids, $this->id, $dest_parent_directory_ids);
    }

    public function parentField(): string {
        return "directory_id";
    }

    public function getName(): string {
        return $this->title;
    }

    public function getAdminUrl(): string {
        return route("admin.directory.show", $this);
    }

    static public function getContentTypes(): array {
        return DirectoryType::toMap();
    }

    public function getFrontUrl(): string {
        if ($this->is_internal_link)
            return $this->url_part ?: "#";
        return lm_url(($this->force_show_landing ? $this->url_landing : $this->url_full));
    }

    public function getLandingUrl(): string {
        $front_url = $this->getFrontUrl();
        return str_ends_with($front_url, "/landing") ? $front_url : $front_url . "/landing";
    }

    public function getParentsUrlFull(): array {
        $url_parts = explode("/", substr($this->url_full, 1));
        $directories_url_full = [];
        $directory_url_full = "";
        foreach ($url_parts as $url_part) {
            $directory_url_full .= "/" . $url_part;
            $directories_url_full[] = $directory_url_full;
        }
        return $directories_url_full;
    }

    public function getParentDirectories() {
        $directories_url_full = $this->getParentsUrlFull();
        return self::whereIn("url_full", $directories_url_full)->orderBy("depth", "ASC")->get();
    }

    public function getParentDirectoriesRecursive(): array {
        if ($this->directory_id == null)
            return [$this];
        return array_merge([$this], $this->parentDirectory->getParentDirectoriesRecursive());
    }

    public function getAllBlog() {
        return $this->leafArticles()->latest()->get();
    }

    public function getPaginatedBlog() {
        return $this->leafArticles()->with("directory")->latest()->paginate(Article::getFrontPaginationCount());
    }

    public function getSearchUrl(): string {
        return route("admin.directory.show", $this);
    }

    public function hasImage(): bool {
        return isset($this->cover_image_path);
    }

    public function getImagePath(): string {
        return $this->cover_image_path;
    }

    public function setImagePath(): void {
        $tmp_image = ImageService::saveImage($this->getImageCategoryName());
        $this->cover_image_path = $tmp_image->destinationPath . "/" . $tmp_image->name;
        $this->save();
    }

    public function removeImage(): void {
        $this->cover_image_path = null;
        $this->save();
    }

    public function getDefaultImagePath(): string {
        return "/admin_dashboard/images/No_image.jpg.png";
    }

    public function getImageCategoryName(): string {
        return "directory";
    }

    public function isImageLocal(): bool {
        return true;
    }

    public function setUrlFull() {
        $this->url_full = (($this->directory_id != null and $this->parentDirectory != null) ?
                $this->parentDirectory->url_full : "") . "/" . $this->url_part;
        $this->save();
    }

    public function attachFileTo(?Directory $dest): void {
        $this->directory_id = $dest?->id;
        $this->save();
        if ($this->linkedLeaves != null)
            $this->attachLeafFiles($this->linkedLeaves->pluck("id")->toArray());
    }

    public function detachFile($dest = null) {
        $this->linkedLeaves = null;
        if ($this->content_type == DirectoryType::PRODUCT)
            $this->linkedLeaves = $this->leafProducts;
        else if ($this->content_type == DirectoryType::BLOG)
            $this->linkedLeaves = $this->leafArticles;

        $parentDirectory = $this->parentDirectory;
        $dest_parent_directory_ids = [];
        if ($dest != null) {
            $destParentDirectories = collect();
            if ($dest->directory_id != null) {
                $destParentDirectory = $dest->parentDirectory;
                while ($destParentDirectory != null) {
                    $destParentDirectories->push($destParentDirectory);
                    $destParentDirectory = $destParentDirectory->parentDirectory;
                }
            }
            $dest_parent_directory_ids = $destParentDirectories->pluck("id")->toArray();
        }
        if ($this->linkedLeaves != null and count($this->linkedLeaves) > 0 and $parentDirectory != null)
            $parentDirectory->detachLeafFiles($this->linkedLeaves->pluck("id")->toArray(), $this->id, $dest_parent_directory_ids);
    }

    public function cloneFile() {
        $this->linkedLeaves = null;
        if ($this->content_type == DirectoryType::PRODUCT)
            $this->linkedLeaves = $this->leafProducts;
        else if ($this->content_type == DirectoryType::BLOG)
            $this->linkedLeaves = $this->leafArticles;
        $newDirectory = $this->replicate();
        $newDirectory->push();
        $newDirectory->linkedLeaves = $this->linkedLeaves;
        return $newDirectory;
    }

    public function generateNewUrls($dest) {
        $oldUrlFull = $this->url_full;
        $this->url_full = ($dest != null and strlen($dest->url_full) > 0) ?
            $dest->url_full . "/" . $this->url_part : $this->url_part;
        $this->save();
        $newUrlFull = $this->url_full;
        if ($oldUrlFull != $newUrlFull) {
            $modifiedUrl = $this->addToModifiedUrls($oldUrlFull, $newUrlFull);
            if (!is_null($modifiedUrl))
                $this->addToRobotsTxtRecords($modifiedUrl);

            if (isset($this->directories) and count($this->directories) > 0)
                foreach ($this->directories as $directory)
                    $directory->generateNewUrls($this);
        }
    }

    public function getHash(): string {
        return md5($this->title . "#" . ($this->is_internal_link != null ? $this->is_internal_link : 0) . "#" .
            ($this->has_web_page != null ? $this->has_web_page : 0) . "#" .
            ($this->priority != null ? $this->priority : 0) . "#" .
            $this->content_type . "#" .
            ($this->show_in_navbar != null ? $this->show_in_navbar : 0) . "#" .
            ($this->show_in_footer != null ? $this->show_in_footer : 0) . "#" .
            ($this->show_in_app_navbar != null ? $this->show_in_app_navbar : 0) . "#" .
            $this->cover_image_path . "#" .
            $this->description . "#" .
            ($this->data_type != null ? $this->data_type : 0));
    }

    public function copyTo($dest) {
        $clone = $this->cloneFile();
        $clone->attachFileTo($dest);
        $clone->generateNewUrls($dest);

        foreach ($this->directories as $directory) {
            $directory->copyTo($clone);
        }
    }

    public function addDirectoryLocation(array $attributes): Model {
        $directory_location = $this->directoryLocations()->create($attributes);
        $this->update([
            "is_location_limited" => true
        ]);

        foreach ($this->directories as $sub_directory)
            $sub_directory->addDirectoryLocation($attributes);

        return $directory_location;
    }

    public function deleteDirectoryLocation(DirectoryLocation $directory_location): ?bool {
        try {
            $this->directoryLocations()->where("state_id", $directory_location->state_id)
                ->where("city_id", $directory_location->city_id)->delete();
            if ($this->directoryLocations()->count() === 0) {
                $this->update([
                    "is_location_limited" => false
                ]);
            }

            foreach ($this->directories as $sub_directory)
                $sub_directory->deleteDirectoryLocation($directory_location);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function hasCustomerMetaCategory(): bool {
        return $this->cmc_id !== null;
    }

    public function hasUniqueCustomerMetaCategory(): bool {
        return $this->hasCustomerMetaCategory() and
            ($this->directory_id === null or
                (
                    $this->cmc_id !== $this->parentDirectory->cmc_id
                )
            );
    }

    public static function create(array $attributes = []) {
        if (!isset($attributes["cmc_id"]) and isset($attributes["directory_id"])) {
            $parent_directory = Directory::find($attributes["directory_id"]);
            if ($parent_directory !== null and $parent_directory->hasCustomerMetaCategory()) {
                $attributes["cmc_id"] = $parent_directory->cmc_id;
            }
        }
        return static::query()->create($attributes);
    }
}
