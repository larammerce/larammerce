<?php

namespace App\Models;

use App\Helpers\ImageHelper;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property integer user_id
 * @property string google_plus
 * @property string twitter
 * @property string facebook
 * @property string telegram
 * @property string instagram
 * @property string blogger
 * @property boolean is_super_user
 * @property boolean is_stock_manager
 * @property boolean is_seo_master
 * @property boolean is_cms_manager
 * @property boolean is_acc_manager
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property User user
 * @property SystemRole[] roles
 * @property Article[] articles
 * @property string main_image_path
 * @property false|string info
 *
 * Class SystemUser
 * @package App\Models
 */
class SystemUser extends BaseModel
{
    protected $table = 'system_users';
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'is_super_user', 'is_stock_manager', 'is_seo_master', 'is_cms_manager', 'is_acc_manager', 'is_expert',
        'main_image_path'
    ];

    protected $casts = [
        "is_super_user" => "bool",
        "is_stock_manager" => "bool",
        "is_seo_master" => "bool",
        "is_cms_manager" => "bool",
        "is_acc_manager" => "bool",
        "is_expert" => "bool"
    ];

    /*
     * Relation Methods
     */

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(SystemRole::class, 'system_user_system_role',
            'system_user_id', 'system_role_id');
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'system_user_id');
    }

    public function hasImage(): bool
    {
        return isset($this->main_image_path);
    }

    public function getImagePath(): ?string
    {
        return $this->main_image_path;
    }

    public function setImagePath()
    {
        $tmpImage = ImageHelper::saveImage($this->getImageCategoryName());
        $this->main_image_path = $tmpImage->destinationPath . '/' . $tmpImage->name;
        $this->save();
    }

    public function removeImage()
    {
        $this->update(['main_image_path' => null]);
    }


    public function getDefaultImagePath(): string
    {
        return '/admin_dashboard/images/No_image.jpg.png';
    }

    public function getImageCategoryName(): string
    {
        return 'system_users_avatar';
    }

    /*
     * Helper Methods
     */

    /**
     * @return array
     */
    public function getPermittedDirectoryTypes(): array
    {
        return Directory::whereHas('systemRoles', function ($query) {
            $query->whereIn('id', $this->roles->pluck('id')->toArray());
        })->pluck('content_type')->unique();
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    public function confirmedMetaItems(): HasMany
    {
        return $this->hasMany(CustomerMetaItem::class, "confirmed_by", "id");
    }
}
