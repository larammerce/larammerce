<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 7/7/18
 * Time: 10:13 PM
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use stdClass;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Properties
 * @property integer id
 * @property string title
 * @property string prefix
 * @property string postfix
 * @property boolean is_assigned
 * @property boolean has_directory
 * @property boolean is_percentage
 * @property DateTime expiration_date
 * @property boolean is_active
 * @property boolean is_event
 * @property integer value
 * @property integer max_amount_supported
 * @property integer min_amount_supported
 * @property boolean is_multi
 * @property boolean has_expiration
 * @property string steps_data
 * @property string steps_data_object
 * @property ProductFilter[] filters
 *
 * Timestamp
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * Relations
 * @property DiscountCard[] discountCards
 *
 * @method static DiscountGroup find(integer $id)
 *
 * Class DiscountGroup
 * @package App\Models
 */
class DiscountGroup extends BaseModel
{
    use SoftDeletes;
    
    protected $table = "discount_groups";
    protected $fillable = [
        "title",
        "prefix",
        "postfix",
        "is_assigned",
        "is_percentage",
        "expiration_date",
        "is_active",
        "is_event",
        "value",
        "max_amount_supported",
        "min_amount_supported",
        "is_multi",
        "has_expiration",
        "has_directory",
        "steps_data"
    ];
    protected $attributes = [
        "steps_data" => "[]"
    ];
    private $extra_attributes = [];

    protected $casts = [
        "expiration_date" => "datetime",
        "is_assigned" => "bool",
        "is_percentage" => "bool",
        "is_active" => "bool",
        "is_event" => "bool",
        "is_multi" => "bool",
        "has_expiration" => "bool",
        "has_directory" => "bool",
    ];

    protected static array $SORTABLE_FIELDS = ["id", "title", "is_assigned", "is_active", "expiration_date"];

    static protected $FRONT_PAGINATION_COUNT = 10;

    public function cards(): HasMany
    {
        return $this->hasMany(DiscountCard::class, "discount_group_id");
    }

    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(ProductFilter::class, "discount_group_product_filter", "discount_group_id", "product_filter_id");
    }


    public function setStepsDataAttribute($value)
    {
        $this->extra_attributes["steps_data_object"] = array_filter(array_map(function ($iter_step) {
            return [
                "amount" => intval($iter_step["amount"]),
                "value" => intval($iter_step["value"])
            ];
        }, ($value ?? [])), function ($iter_step) {
            return $iter_step["value"] != 0 or $iter_step["amount"] != 0;
        });
        $this->attributes["steps_data"] = json_encode($this->extra_attributes["steps_data_object"]);
    }

    public function getStepsDataObjectAttribute()
    {
        if (!isset($this->extra_attributes["steps_data_object"])) {
            $tmp_result = json_decode($this->steps_data);
            if (!is_array($tmp_result))
                $tmp_result = [];
            $this->extra_attributes["steps_data_object"] = $tmp_result;
            $first_step = new stdClass();
            $first_step->amount = "0";
            $first_step->value = "{$this->value}";
            $this->extra_attributes["steps_data_object"] = Arr::prepend($this->extra_attributes["steps_data_object"], $first_step);
        }
        return $this->extra_attributes["steps_data_object"];
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return "";
    }

    public function calculate(int $discountable_amount): int
    {
        $value = $this->value;
        $steps_data = ($this->steps_data !== null and strlen($this->steps_data)) ? json_decode($this->steps_data) : null;
        if ($steps_data !== null) {
            foreach ($steps_data as $step) {
                if ($discountable_amount > $step->amount and $step->value > $value) {
                    $value = $step->value;
                }
            }
        }

        return $value;
    }
}
