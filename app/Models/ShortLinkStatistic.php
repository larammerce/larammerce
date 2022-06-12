<?php

namespace App\Models;

use DateTime;

/**
 * @property integer id
 * @property integer short_link_id
 * @property integer views_count
 * @property string json_data
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property ShortLink shortLink
 *
 * Class ShortLinkStatistic
 * @package App\Models
 */
class ShortLinkStatistic extends BaseModel
{
    protected $table = 'short_link_statistics';

    public function shortLink(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\ShortLink', 'short_link_id');
    }

    public function getSearchUrl(): string
    {
        return "";
    }
}
