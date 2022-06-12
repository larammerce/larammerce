<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use DateTime;

/**
 * @property integer $id
 * @property integer customer_user_id
 * @property integer product_id
 * @property integer count
 * @property integer cmi_id
 * @property DateTime created_at
 * @property DateTime updated_at
 * @property DateTime customer_notified_at
 * @property DateTime customer_viewed_at
 * @property Product product
 * @property CustomerUser customer
 * @property CustomerMetaItem customerMetaItem
 *
 * Class CartRow
 * @package App\Models
 */
class CartRow extends BaseModel
{
    protected $table = 'customer_carts';
    protected $fillable = [
        'customer_user_id', 'product_id', 'count', "cmi_id",
        'customer_notified_at', 'customer_viewed_at'
    ];

    protected static array $SORTABLE_FIELDS = ['id', 'customer_user_id'];
    static protected int $FRONT_PAGINATION_COUNT = 10;

    protected $casts = [
        "customer_notified_at" => "timestamp",
        "customer_viewed_at" => "timestamp"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo("\\App\\Models\\CustomerUser", "customer_user_id");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo("\\App\\Models\\Product", "product_id");
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return "";
    }

    public function customerMetaItem(): BelongsTo
    {
        return $this->belongsTo(CustomerMetaItem::class, "cmi_id", "id");
    }
}
