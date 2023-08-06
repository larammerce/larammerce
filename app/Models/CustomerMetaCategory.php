<?php


namespace App\Models;

use App\Traits\WithDataField;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * @property integer id
 * @property string title
 * @property boolean needs_admin_confirmation
 * @property integer parent_id
 * @property string form_blade_name
 * @property string data
 * @property stdClass data_object
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property CustomerMetaItem[] items
 *
 * @method static Builder main()
 *
 * Class CustomerMetaCategory
 * @package App\Models
 */
class CustomerMetaCategory extends BaseModel
{
    use WithDataField;

    protected $table = "customer_meta_categories";
    protected $fillable = [
        "title", "needs_admin_confirmation", "form_blade_name", "data", "parent_id"
    ];
    protected $attributes = [
        "data" => "[]"
    ];

    protected static array $SORTABLE_FIELDS = [
        "id", "title"
    ];
    protected static array $SEARCHABLE_FIELDS = [
        "title"
    ];

    public function getSearchUrl(): string
    {
        return "";
    }

    public function items(): HasMany
    {
        return $this->hasMany(CustomerMetaItem::class, "cmc_id", "id");
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CustomerMetaCategory::class, "parent_id", "id");
    }

    public function children(): HasMany
    {
        return $this->hasMany(CustomerMetaCategory::class, "parent_id", "id");
    }

    public function scopeMain($query)
    {
        return $query->where("parent_id", DB::raw("id"))->orWhere("parent_id", null);
    }
}
