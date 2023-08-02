<?php

namespace App\Models;

use App\Interfaces\RateOwnerInterface as RateableContract;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property integer id
 * @property integer object_id
 * @property string object_type
 * @property integer value
 * @property string comment
 * @property integer customer_user_id
 * @property boolean is_accepted
 * @property boolean is_reviewed
 * @property DateTime created_at
 * @property DateTime updated_at
 *
 * @property CustomerUser customerUser
 * @property RateableContract object
 *
 * Class Rate
 * @package App\Models
 */
class Rate extends BaseModel
{
    protected $table = 'rates';
    public $timestamps = true;

    protected $fillable = [
        'object_id', 'object_type', 'value', 'comment', 'customer_user_id', 'is_accepted', 'is_reviewed'
    ];

    protected static array $SORTABLE_FIELDS = [
        'id', 'is_reviewed', 'created_at'
    ];


    /*
     * Relation Methods
     */

    /**
     * @return BelongsTo
     */
    public function customerUser()
    {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    /**
     * @return MorphTo
     */
    public function object()
    {
        return $this->morphTo();
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeAuthCustomer($query)
    {
        return $query->where('customer_user_id', get_customer_user()->id);
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }

}
