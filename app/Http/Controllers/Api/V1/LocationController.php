<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/19/17
 * Time: 11:39 AM
 */

namespace App\Http\Controllers\Api\V1;

use App\Models\City;
use App\Models\State;

/**
 * Class ShopController
 * @package App\Http\Controllers\Api\V1
 */
class LocationController extends BaseController
{
    /**
     * @description(return="states[]", request_method="GET", comment="this api will return list of all states")
     */
    public function getStates()
    {
        return State::all();
    }


    /**
     * @rules(state_id="required|exists:states,id")
     * @description(return="cities[]", request_method="GET", comment="this api will return cities for selected state")
     */
    public function getCities()
    {
        return State::find(request('state_id'))->cities;
    }

    /**
     * @rules(city_id="required|exists:cities,id")
     * @description(return="districts[]", request_method="GET", comment="this api will return districts for selected city")
     */
    public function getDistricts()
    {
        $city = City::find(request('city_id'));
        if ($city->has_district)
            return $city->districts;
        return [];
    }
}