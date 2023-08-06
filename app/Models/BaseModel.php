<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/15/16
 * Time: 4:42 PM
 */

namespace App\Models;

use App\Scopes\SortScope;
use App\Utils\CMS\AdminRequestService;
use App\Utils\CMS\Setting\Layout\LayoutService;
use App\Utils\Jalali\JDate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 *
 * @method static Builder search(string $term)
 * @method static static find(int | string $id)
 * @method static static findOrFail(int | string $id)
 * @method static static create(array $attributes)
 * @method static Builder where(string $col_name, mixed $param_two, mixed $param_three = null)
 * @method static Builder whereIn(string $col_name, array $values)
 *
 * Class BaseModel
 * @package App\Models
 */
abstract class BaseModel extends Model
{

    protected $guarded = [];

    protected array $show_list_types = [
        'grid',
        'list'
    ];

    protected static array $SORTABLE_FIELDS = [
        'id'
    ];

    protected static array $PAGINATION_COUNT = [
        'list' => 9,
        'grid' => 16
    ];

    protected static array $SEARCHABLE_FIELDS = [
        'id'
    ];

    protected static ?string $IMPORTANT_SEARCH_FIELD = "id";
    protected static ?string $EXACT_SEARCH_ORDER_FIELD = null;

    protected static array $ROLE_PROPERTY_ACCESS = [
        "super_user" => [
            "*"
        ],
        "stock_manager" => [
            "*"
        ],
        "seo_master" => [
            "*"
        ],
        "cms_manager" => [
            "*"
        ],
        "acc_manager" => [
            "*"
        ],
        "expert" => [
            "*"
        ]
    ];

    protected static array $IMPORTABLE_ATTRIBUTES = [];
    protected static array $EXPORTABLE_RELATIONS = [];

    public function scopeExactSearch($builder, string $term): Builder {
        $builder->where(function ($q) use ($term) {
            foreach (static::$SEARCHABLE_FIELDS as $searchable_field) {
                $q->orWhere($searchable_field, "=", $term);
            }
        });

        return $builder;
    }

    public function scopeSearch(Builder $builder, string $term, int $exactness = 0): Builder {
        $term = preg_replace("/[ ]+/", " ", $term);
        $termParts = explode(" ", $term);
        foreach (static::$SEARCHABLE_FIELDS as $fieldIndex => $searchableField) {
            foreach ($termParts as $stringPartIndex => $termPart) {
                if ($fieldIndex == 0 and $stringPartIndex == 0)
                    $builder->where($searchableField, 'LIKE', '%' . $termPart . '%');
                else
                    $builder->orWhere($searchableField, 'LIKE', '%' . $termPart . '%');
            }
        }
        return $builder;
    }

    public function scopeClassicSearch(Builder $builder, array $term): Builder {
        foreach ($term as $key => $value) {
            if ($value !== null && in_array($key, static::$SEARCHABLE_FIELDS)) {
                $builder->where($key, 'LIKE', '%' . $value . '%');
            }
        }
        return $builder;
    }

    public abstract function getSearchUrl(): string;

    public function toArray(): array {
        $parentResponse = parent::toArray();
        foreach ($parentResponse as $key => $value) {
            if (is_string($value) and str_ends_with($key, "_at")) {
                $parentResponse[$key . "_jalali"] = JDate::forge($value)->format("Y/m/d H:i");
            }
        }
        $parentResponse["search_url"] = $this->getSearchUrl();//TODO: this should be moved to is in admin area section
        $parentResponse["class_name"] = get_class($this);
        return $parentResponse;
    }

    protected static function boot(): void {
        parent::boot();

        if (AdminRequestService::isInAdminArea()) {
            static::addGlobalScope(new SortScope());
        }
    }

    public static function getPaginationCount(): int {
        if (is_array(static::$PAGINATION_COUNT)) {
            $layoutMethod = LayoutService::getRecord(get_called_class())->getMethod();
            return static::$PAGINATION_COUNT[$layoutMethod];
        }
        return intval(static::$PAGINATION_COUNT);
    }

    public static function getSortableFields(): array {
        return static::$SORTABLE_FIELDS;
    }

    public static function getSearchableFields(): array {
        return static::$SEARCHABLE_FIELDS;
    }

    public static function getImportableAttributes(): array {
        return static::$IMPORTABLE_ATTRIBUTES;
    }

    public static function getExportableRelations(): array {
        return static::$EXPORTABLE_RELATIONS;
    }

    public static function getExportableAttributes(array $to_merge = []): array {
        $model = new static();
        return [
            ...array_diff(Schema::getColumnListing($model->getTable()), $model->getHidden()),
            ...$to_merge
        ];
    }

    public function getAllowedInputs(): array {
        $result = [];
        if (AdminRequestService::isInAdminArea()) {
            $systemUser = get_system_user();
            if ($systemUser !== null) {
                foreach (static::$ROLE_PROPERTY_ACCESS as $role => $fillable) {
                    if ($systemUser->{"is_" . $role}) {
                        if (in_array("*", $fillable))
                            return $this->getFillable();
                        $result = array_unique(array_merge($result, $fillable), SORT_REGULAR);
                    }
                }
            }
        } else {
            $result = $this->getFillable();
        }
        return $result;
    }

    public function isInputAllowed(string $input): bool {
        return in_array($input, $this->getAllowedInputs());
    }

    public function update(array $attributes = [], array $options = []): bool {
        $allowed_attributes = [];
        $allowed_inputs = $this->getAllowedInputs();
        foreach ($attributes as $attr_key => $attr_value) {
            if (in_array($attr_key, $allowed_inputs) and (key_exists($attr_key, $this->attributes) or in_array($attr_key, $this->appends))) {
                $allowed_attributes[$attr_key] = $attr_value;
            }
        }
        return parent::update($allowed_attributes, $options);
    }
}
