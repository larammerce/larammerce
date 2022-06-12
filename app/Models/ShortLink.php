<?php

namespace App\Models;

use DateTime;

/**
 * @property integer id
 * @property string link
 * @property string shortened_link
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * Class ShortLink
 * @package App\Models
 */
class ShortLink extends BaseModel
{
    protected $table = 'short_links';

    protected $fillable = [
        'link', 'shortened_link'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'created_at'];
    protected static array $ROLE_PROPERTY_ACCESS = [
        "super_user" => ["*"],
        "cms_manager" => [
            'data', 'skip_count', 'take_count', 'query_data'
        ]
    ];
    protected static array $SEARCHABLE_FIELDS = ["id", "link", "shortened_link"];

    public static function findByShortenedLink($shortened_link): ShortLink
    {
        return static::where('shortened_link', $shortened_link)->firstOrFail();
    }

    public function statistics()
    {
        return $this->hasmany('App\Models\ShortLinkStatistic', 'short_link_id');
    }

    public function getSearchUrl(): string
    {
        return '';
    }
}
