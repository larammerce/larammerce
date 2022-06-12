<?php

namespace App\Models;

use App\Utils\CMS\InvoiceService;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationModel;
use App\Utils\CMS\Setting\CustomerLocation\CustomerLocationService;

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


    /*
     * Relations Methods
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district()
    {
        return $this->belongsTo('\\App\\Models\\District', 'district_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoices()
    {
        return $this->hasMany('\\App\\Models\\Invoice', 'customer_address_id');
    }

    public function scopeMain($query)
    {
        return $query->where("is_main", true);
    }

    public function setAsMain()
    {
        if (!$this->is_main) {
            $mainAddress = $this->customer->addresses()->main()->first();
            if ($mainAddress != null) {
                $mainAddress->is_main = false;
                $mainAddress->save();
            }
            $this->is_main = true;
            $this->save();
            $this->setAsCurrentLocation();
            InvoiceService::updateAddress($this);
        }
        return true;
    }

    public function setAsCurrentLocation()
    {
        CustomerLocationService::setRecord(new CustomerLocationModel($this->state, $this->city));
    }


    /*
     * Helper method
     */
    public function getFullAddress()
    {
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
    public function getSearchUrl(): string
    {
        return '';
    }
}
