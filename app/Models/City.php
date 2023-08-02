<?php

namespace App\Models;

use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string name
 * @property integer state_id
 * @property boolean has_district
 *
 * @property State state
 * @property District[] districts
 * @property CustomerAddress[] customerAddresses
 *
 * Class City
 * @package App\Models
 */
class City extends BaseModel implements TaggableContract
{
    use Taggable, Translatable;

    protected $table = 'cities';

    protected $fillable = [
        'name', 'state_id', 'has_district'
    ];

    public $timestamps = false;

    static protected array $SORTABLE_FIELDS = ['id', 'name'];

    protected static array $SEARCHABLE_FIELDS = ['name'];

    protected static array $TRANSLATABLE_FIELDS = [
        'name' => ['string', 'input:text']
    ];

    /*
     * Relations Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('\\App\\Models\\State', 'state_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function districts()
    {
        return $this->hasMany('App\\Models\\District', 'city_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerAddresses()
    {
        return $this->hasMany('App\\Models\\CustomerAddress', 'city_id');
    }

    public function getText()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }


    public function directoryLocations(): HasMany
    {
        return $this->hasMany(DirectoryLocation::class, "city", "id");
    }
}
