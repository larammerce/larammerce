<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer id
 * @property string title
 * @property integer customer_user_id
 * @property integer amount
 * @property Carbon used_at
 * @property integer invoice_id
 * @property Carbon expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property boolean is_used
 *
 * @property CustomerUser customer
 * @property Invoice invoice
 */
class Coupon extends BaseModel {

    protected $table = "coupons";
    protected $fillable = [
        "title",
        "customer_user_id",
        "amount",
        "invoice_id",
        "used_at",
        "expire_at"
    ];

    protected $appends = [
        "is_used"
    ];

    protected $casts = [
        "used_at" => "datetime",
        "expire_at" => "datetime"
    ];

    protected static array $SORTABLE_FIELDS = [
        "id",
        "created_at",
        "used_at",
        "expire_at"
    ];

    protected static array $SEARCHABLE_FIELDS = [
        "id",
        "customer_user_id",
        "title"
    ];

    public function getIsUsedAttribute(): bool {
        return $this->used_at !== null;
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, "customer_user_id", "id");
    }

    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class, "invoice_id", "id");
    }

    public function getSearchUrl(): string {
        return "";
    }
}