<?php


namespace App\Http\Controllers\Customer;

use App\Features\CustomerLocation\CustomerLocationConfig;
use App\Features\CustomerLocation\CustomerLocationSettingData;
use App\Models\City;
use App\Models\State;
use App\Utils\CMS\SystemMessageService;

/**
 * Class LocationController
 * @package App\Http\Controllers\Customer
 */
class LocationController extends BaseController
{
    /**
     * @rules(state_id="required|exists:states,id", city_id="required|exists:cities,id")
     */
    public function store()
    {
        $state = State::find(request()->get("state_id"));
        $city = City::find(request()->get("city_id"));
        CustomerLocationConfig::setRecord(new CustomerLocationSettingData($state, $city));
        SystemMessageService::addSuccessMessage("system_messages.user.location_updated");
        return redirect()->back();
    }
}
