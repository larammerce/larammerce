<?php

namespace App\Models;

use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;
use App\Utils\Translation\Traits\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string name
 *
 * @property City[] cities
 * @property CustomerAddress[] customerAddresses
 *
 * Class State
 * @package App\Models
 */
class State extends BaseModel implements TaggableContract
{
    use Taggable, Translatable;

    protected $table = 'states';

    protected $fillable = [
        'name'
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany('App\\Models\\City', 'state_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customerAddresses()
    {
        return $this->hasMany('App\\Models\\CustomerAddress', 'state_id');
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
        return $this->hasMany(DirectoryLocation::class, "state_id", "id");
    }
}
