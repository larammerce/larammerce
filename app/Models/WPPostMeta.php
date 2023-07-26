<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method Builder thumbnailId()
 */
class WPPostMeta extends BaseModel
{
    protected $connection = 'wp_connection';
    protected $table = 'wp_postmeta';
    protected $primaryKey = 'meta_id';

    public function post(): BelongsTo {
        return $this->belongsTo(WPPost::class, 'post_id');
    }

    public function scopeThumbnailId(Builder $query): Builder {
        return $query->where("meta_key", "_thumbnail_id");
    }

    public function getSearchUrl(): string {
        return "";
    }
}
