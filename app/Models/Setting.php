<?php

namespace App\Models;

use App\Utils\CMS\Setting\AbstractSettingModel;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * @property integer id
 * @property string key
 * @property string value
 * @property AbstractSettingModel data
 * @property integer user_id
 * @property boolean is_system_setting
 *
 * @property User user
 *
 * @method static Builder globalData()
 * @method static Builder localData()
 * @method static Builder systemSettings()
 * @method static Builder nonSystemSettings()
 * @method static Builder userSettings()
 * @method static Builder cmsRecords()
 * @method static static create(array $attributes)
 *
 * Class Setting
 * @package App\Models
 */
class Setting extends Model
{
    protected $table = 'settings';

    protected $fillable = [
        'key', 'value', 'user_id', 'is_system_setting'
    ];

    static protected int $PAGINATION_COUNT = 9;

    protected static array $SEARCHABLE_FIELDS = [
        'id', 'key', 'value'
    ];

    protected array $caches = [];

    protected static ?string $IMPORTANT_SEARCH_FIELD = null;
    protected static ?string $EXACT_SEARCH_ORDER_FIELD = null;
    protected static array $ONE_TO_ONE_RELATIONS = [];
    protected static array $EXPORTABLE_RELATIONS = [];

    public function getDataAttribute(): ?AbstractSettingModel
    {
        if (!isset($this->caches["data"]))
            try {
                $this->caches["data"] = unserialize($this->value);
            } catch (Exception $e) {
                $this->delete();
                return null;
            }
        return $this->caches["data"];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function scopeGlobalData($query)
    {
        return $query->whereNull("user_id");
    }

    public function scopeLocalData($query)
    {
        return $query->where("user_id", Auth::user()->id);
    }

    public function scopeSystemSettings($query)
    {
        return $query->where("is_system_setting", true);
    }

    public function scopeNonSystemSettings($query)
    {
        return $query->where("is_system_setting", false);
    }

    public function scopeUserSettings($query)
    {
        return $query->where("user_id", null)->orWhere("user_id", "=", Auth::user()->id);
    }

    public function scopeCMSRecords($query)
    {
        return $query->globalData()->nonSystemSettings();
    }

    public function scopeClassicSearch(Builder $builder, array $term): Builder
    {
        $builder->userSettings()->nonSystemSettings();
        foreach ($term as $key => $value) {
            if ($value !== null && in_array($key, static::$SEARCHABLE_FIELDS)) {
                $builder->where($key, 'LIKE', '%' . $value . '%');
            }
        }
        return $builder;
    }

    public static function getPaginationCount()
    {
        return self::$PAGINATION_COUNT;
    }

    public static function getCMSRecord($key)
    {
        $cache_key = "CMSRecord:{$key}";
        $result = "";
        if (Cache::has($cache_key)) {
            $result = Cache::get($cache_key);
        } else {
            try {
                $result = Setting::cmsRecords()->where("key", $key)->firstOrFail();
                Cache::put($cache_key, $result, 1);
            } catch (Exception $e) {
                Cache::put($cache_key, null, 1);
                throw $e;
            }
        }
        return $result;
    }

    public static function getSearchableFields(): array
    {
        return static::$SEARCHABLE_FIELDS;
    }

    public function getSearchUrl()
    {
        return '';
    }

    public static function getExportableAttributes(): array
    {
        $model = new static();
        return array_diff($model->getfillable(), $model->getHidden());
    }

    public static function getExportableRelations(): array
    {
        return static::$EXPORTABLE_RELATIONS;
    }

    public static function getOneToOneRelations(): array
    {
        return static::$ONE_TO_ONE_RELATIONS;
    }
}
