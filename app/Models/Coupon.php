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
 * @property Carbon expire_at
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * @property CustomerUser customer
 */
class Coupon extends BaseModel {

    protected $table = "coupons";
    protected $fillable = [
        "title",
        "customer_user_id",
        "amount",
        "used_at",
        "expire_at"
    ];

    protected $casts = [
        "used_at" => "timestamp",
        "expire_at" => "timestamp"
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

    public function customer(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, "customer_user_id", "id");
    }

    public function getSearchUrl(): string {
        return "";
    }
}