<?php


namespace App\Utils\CMS\Setting\ShipmentCost;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;

/**
 * @method static ShipmentCostModel getRecord(string $name = "", ?string $parent_id = null)
 */
class ShipmentCostService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = 'shipment_cost_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): ShipmentCostModel
    {
        return new ShipmentCostModel();
    }
}
