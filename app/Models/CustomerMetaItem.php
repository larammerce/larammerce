<?php


namespace App\Models;

use App\Traits\WithDataField;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

/**
 * @property integer id
 * @property integer cmc_id
 * @property integer confirmed_by
 * @property integer customer_user_id
 * @property string data
 * @property stdClass data_object
 * @property DateTime admin_viewed_at
 * @property boolean is_confirmed
 * @property boolean is_main
 * @property boolean is_hidden
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property CustomerMetaCategory category
 * @property SystemUser admin
 * @property CustomerUser customer
 * @property CartRow[] cartRows
 * @property InvoiceRow[] invoiceRows
 *
 * Class CustomerMetaCategory
 * @package App\Models
 */
class CustomerMetaItem extends BaseModel
{
    use WithDataField;

    protected $table = "customer_meta_items";
    protected $casts = [
        "admin_viewed_at" => "timestamp"
    ];
    protected $fillable = [
    ];
    protected $attributes = [
        "data" => "[]"
    ];

    protected static array $SORTABLE_FIELDS = [
    ];
    protected static array $SEARCHABLE_FIELDS = [
    ];

    public function getSearchUrl(): string
    {
        return "";
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CustomerMetaCategory::class, "cmc_id", "id");
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(SystemUser::class, "confirmed_by", "id");
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, "customer_user_id", "id");
    }

    public function scopeVisible($query)
    {
        return $query->where("is_hidden", false);
    }

    public function cartRows(): HasMany
    {
        return $this->hasMany(CartRow::class, "cmi_id", "id");
    }

    public function invoiceRows(): HasMany
    {
        return $this->hasMany(InvoiceRow::class, "cmi_id", "id");
    }
}
