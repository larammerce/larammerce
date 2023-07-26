<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property integer directory_id
 * @property integer state_id
 * @property integer city_id
 *
 * @property Directory directory
 * @property State state
 * @property City city
 *
 * Class DirectoryLocation
 * @package App\Models
 */
class DirectoryLocation extends BaseModel
{
    protected $table = 'directory_location';
    protected $fillable = [
        'directory_id', 'state_id', 'city_id'
    ];

    public $timestamps = false;

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

    public function directory(): BelongsTo
    {
        return $this->belongsTo(Directory::class, "directory_id", "id");
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, "state_id", "id");
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, "city_id", "id");
    }

    public function newInstance($attributes = [], $exists = false)
    {
        if (isset($attributes["city_id"]) and
            is_string($attributes["city_id"]) and
            strlen($attributes["city_id"]) === 0) {
            $attributes["city_id"] = null;
        }
        return parent::newInstance($attributes, $exists);
    }
}
