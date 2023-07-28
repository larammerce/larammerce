<?php


namespace App\Features\ShipmentCost;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;

/**
 * @method static ShipmentCostSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class ShipmentCostConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = 'shipment_cost_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): ShipmentCostSettingData
    {
        return new ShipmentCostSettingData();
    }
}
