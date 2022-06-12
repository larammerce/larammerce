<?php

namespace App\Models;

/**
 * @property integer id
 * @property integer customer_user_id
 * @property string company_name
 * @property string economical_code
 * @property string national_id
 * @property string registration_code
 * @property string company_phone
 * @property string fin_relation
 * @property integer state_id
 * @property integer city_id
 *
 * @property CustomerUser customerUser
 * @property State state
 * @property City city
 *
 * Class CustomerUserLegalInfo
 * @package App\Models
 */
class CustomerUserLegalInfo extends BaseModel
{
    protected $table = 'customer_users_legal_info';
    public $timestamps = false;

    protected $fillable = [
        'customer_user_id', 'company_name', 'economical_code', 'national_id',
        'registration_code', 'company_phone', 'state_id', 'city_id', 'fin_relation'
    ];

    protected static array $SORTABLE_FIELDS = ['id'];

    protected static array $SEARCHABLE_FIELDS = [
        'id',
        'company_name',
        'economical_code',
        'national_id',
        'registration_code',
        'company_phone',
        'fin_relation'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerUser()
    {
        return $this->belongsTo('\\App\\Models\\CustomerUser', 'customer_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('\\App\\Models\\State', 'state_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('\\App\\Models\\City', 'city_id');
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string
    {
        return '';
    }
}
