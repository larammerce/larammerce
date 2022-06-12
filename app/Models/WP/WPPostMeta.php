<?php


namespace App\Models\WP;


use App\Models\BaseModel;

class WPPostMeta extends BaseModel
{
    protected $connection = 'wp_connection';
    protected $table = 'wp_postmeta';
    protected $primaryKey = 'meta_id';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(WPPost::class, 'post_id');
    }

    public function scopeThumbnailId($query)
    {
        return $query->where("meta_key", "_thumbnail_id");
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
}
