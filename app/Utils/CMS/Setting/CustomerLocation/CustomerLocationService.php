<?php


namespace App\Utils\CMS\Setting\CustomerLocation;


use App\Interfaces\SettingDataInterface;
use App\Models\City;
use App\Models\State;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use Exception;
use function request;

class CustomerLocationService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_customer_location';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::SESSION;

    public static function getRecord(string $name = "", ?string $parent_id = null): null|CustomerLocationModel|SettingDataInterface
    {
        try {
            return parent::getRecord($name, $parent_id);
        } catch (Exception $e) {
            $state = request()->has("state_id") ? State::find(request()->get("state_id")) : new State();
            $city = request()->has("city_id") ? City::find(request()->get("city_id")) : new City();

            $result = new CustomerLocationModel($state, $city);
            return $result->validate() ? $result : null;
        }
    }
}
