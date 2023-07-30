<?php

namespace App\Models;

use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationModel;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationService;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer id
 * @property string name
 * @property integer customer_user_id
 * @property integer state_id
 * @property integer city_id
 * @property integer district_id
 * @property string phone_number
 * @property string zipcode
 * @property string superscription
 * @property string transferee_name
 * @property boolean is_main
 *
 * @property CustomerUser customer
 * @property State state
 * @property City city
 * @property District district
 * @property Invoice[] invoices
 *
 * @method static CustomerAddress find(integer $id)
 *
 * Class CustomerAddress
 * @package App\Models
 */
class CustomerAddress extends BaseModel
{
    protected $table = 'customer_addresses';

    protected $fillable = [
        'customer_user_id', 'state_id', 'city_id', 'district_id', 'phone_number', 'zipcode', 'superscription',
        'transferee_name', 'name'
    ];

    public $timestamps = false;

    protected static array $SORTABLE_FIELDS = ['id', 'name'];

    public function customer(): BelongsTo {
        return $this->belongsTo(CustomerUser::class, 'customer_user_id');
    }

    public function state(): BelongsTo {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city(): BelongsTo {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function district(): BelongsTo {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function invoices(): HasMany {
        return $this->hasMany(Invoice::class, 'customer_address_id');
    }

    public function scopeMain($query) {
        return $query->where("is_main", true);
    }

    public function setAsCurrentLocation() {
        CustomerLocationService::setRecord(new CustomerLocationModel($this->state, $this->city));
    }


    /*
     * Helper method
     */
    public function getFullAddress() {
        $separator = '، ';
        $result = $this->state->name . $separator;
        $result .= $this->city->name . $separator;
        $result .= $this->superscription . " .  ";
        $result .= trans('ecommerce.user.zipCode') . ':' . $this->zipcode;
        return $result;
    }

    /**
     * @return string
     */
    public function getSearchUrl(): string {
        return '';
    }
}
