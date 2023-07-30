<?php

namespace App\Models;

use App\Interfaces\TagInterface as TaggableContract;
use App\Traits\Taggable;

/**
 * @property integer id
 * @property string name
 *
 * @property SystemUser[] users
 * @property Directory[] directories
 *
 * Class SystemRole
 * @package App\Models
 */
class SystemRole extends BaseModel implements TaggableContract
{
    use Taggable;

    protected $table = 'system_roles';

    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('\\App\\Models\\SystemUser', 'system_user_system_role',
            'system_role_id', 'system_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function directories()
    {
        return $this->belongsToMany('\\App\\Models\\Directory', 'directory_system_role',
            'system_role_id', 'directory_id');
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
}
