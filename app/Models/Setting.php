<?php

namespace App\Models;

use App\Interfaces\SettingDataInterface;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

/**
 * @property integer id
 * @property string key
 * @property string value
 * @property SettingDataInterface data
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

    protected array $cached_attributes = [];

    protected static ?string $IMPORTANT_SEARCH_FIELD = null;
    protected static ?string $EXACT_SEARCH_ORDER_FIELD = null;
    protected static array $ONE_TO_ONE_RELATIONS = [];
    protected static array $EXPORTABLE_RELATIONS = [];

    public function getDataAttribute(): ?SettingDataInterface {
        if (!isset($this->cached_attributes["data"]))
            try {
                $this->cached_attributes["data"] = unserialize($this->value);
            } catch (Exception $e) {
                $this->delete();
                return null;
            }
        return $this->cached_attributes["data"];
    }

    public function setDataAttribute(SettingDataInterface $data): void {
        $this->cached_attributes["data"] = $data;
        $this->value = serialize($data);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, "user_id");
    }

    public function scopeGlobalData(Builder $query) {
        return $query->whereNull("user_id");
    }

    public function scopeLocalData(Builde $query) {
        return $query->where("user_id", Auth::user()->id);
    }

    public function scopeSystemSettings(Builder $query) {
        return $query->where("is_system_setting", true);
    }

    public function scopeNonSystemSettings(Builder $query) {
        return $query->where("is_system_setting", false);
    }

    public function scopeUserSettings(Builder $query) {
        return $query->where("user_id", null)->orWhere("user_id", "=", Auth::user()->id);
    }

    public function scopeCMSRecords(Builder $query) {
        return $query->globalData()->nonSystemSettings();
    }

    public function scopeClassicSearch(Builder $builder, array $term): Builder {
        $builder->userSettings()->nonSystemSettings();
        foreach ($term as $key => $value) {
            if ($value !== null && in_array($key, static::$SEARCHABLE_FIELDS)) {
                $builder->where($key, 'LIKE', '%' . $value . '%');
            }
        }
        return $builder;
    }

    public static function getPaginationCount() {
        return self::$PAGINATION_COUNT;
    }

    public static function getSearchableFields(): array {
        return static::$SEARCHABLE_FIELDS;
    }

    public function getSearchUrl() {
        return '';
    }

    public static function getExportableAttributes(array $to_merge = []): array {
        $model = new static();
        return [
            ...array_diff(Schema::getColumnListing($model->getTable()), $model->getHidden()),
            ...$to_merge
        ];
    }

    public static function getExportableRelations(): array {
        return static::$EXPORTABLE_RELATIONS;
    }

    public static function getOneToOneRelations(): array {
        return static::$ONE_TO_ONE_RELATIONS;
    }
}
