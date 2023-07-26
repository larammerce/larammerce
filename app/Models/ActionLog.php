<?php

namespace App\Models;

use App\Interfaces\SystemLogInterface;
use App\Traits\Searchable;
use App\Utils\CMS\Setting\Layout\LayoutService;
use Illuminate\Database\Eloquent\Builder;
use Jenssegers\Mongodb\Eloquent\Model;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @property string action
 * @property string user_agent_ip
 * @property string user_agent_title
 * @property boolean is_allowed
 * @property array url_parameters
 * @property array request_data
 * @property User user
 * @property integer user_id
 * @property string _id
 * @property string message
 * @property string related_model_type
 * @property integer related_model_id
 * @property string method_name
 */
class ActionLog extends Model implements SystemLogInterface
{
    use Searchable;

    protected $connection = 'mongodb';
    protected $collection = 'action_logs';
    public $timestamps = true;

    protected $fillable = ['user', 'action', 'user_agent_ip', 'user_agent_title',
        'url_parameters', 'request_data', 'is_allowed', 'related_model_id', 'related_model_type',
        'user_id', 'method_name', 'message'];

    protected static array $SORTABLE_FIELDS = ['created_at', 'is_allowed'];
    protected static array $SEARCHABLE_FIELDS = ['_id', 'related_model_id', 'user_id'];

    protected array $show_list_types = [
        'grid',
        'list'
    ];

    protected static array $PAGINATION_COUNT = [
        'list' => 10,
        'grid' => 8
    ];

    public static function getPaginationCount(): int
    {
        if (is_array(static::$PAGINATION_COUNT)) {
            $layoutMethod = LayoutService::getRecord(get_called_class())->getMethod();
            return static::$PAGINATION_COUNT[$layoutMethod];
        }
        return intval(static::$PAGINATION_COUNT);
    }

    public static function getSortableFields(): array
    {
        return static::$SORTABLE_FIELDS;
    }

    public static function getSearchableFields(): array
    {
        return static::$SEARCHABLE_FIELDS;
    }

    public function scopeFilter(Builder $builder, array $terms): Builder
    {
        foreach ($terms as $key => $value) {
            if ($value !== null) {
                if (is_array($value) and sizeof($value) > 1)
                    $builder->whereBetween($key, $value);
                else
                    $builder->where($key, $value);
            }
        }
        return $builder;
    }

    public function translate()
    {
        //TODO: fill this with proper message
        $user = $this->user;
        $this->message = '<a>' . "#$this->_id log action: $this->action for user: $user->name" . '</a>';
    }

    public function setUserAttribute(User $user)
    {
        $this->attributes["user"] = $user->toArray();
        $this->attributes["user_id"] = $user->id;
    }

    public function getUserAttribute()
    {
        if (!isset($this->cast_attributes["user"])) {
            $user = new User();
            User::unguard();
            $user->fill($this->attributes["user"]);
            User::reguard();
            $this->cast_attributes["user"] = $user;
        }
        return $this->cast_attributes["user"];
    }

    public function getMethodNameAttribute(): string
    {
        if (!isset($this->cast_attributes["method_name"]) or
            $this->cast_attributes["method_name"] == null) {
            $action = $this->attributes['action'];
            if (strlen($action) > 0 and str_contains($action, "@")) {
                $arr = explode('@', $action);
                if (sizeof($arr) > 1)
                    $this->cast_attributes["method_name"] = $arr[1];
            } else
                $this->cast_attributes["method_name"] = null;
        }
        return $this->cast_attributes["method_name"];
    }


}
