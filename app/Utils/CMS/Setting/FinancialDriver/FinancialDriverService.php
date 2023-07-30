<?php

namespace App\Utils\CMS\Setting\FinancialDriver;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\FinancialManager\Exceptions\FinancialDriverInvalidConfigurationException;
use App\Utils\FinancialManager\Exceptions\FinancialDriverNotConfiguredException;
use App\Utils\FinancialManager\Models\BaseFinancialConfig;

/**
 *
 * @method static FinancialDriverModel getRecord(string $name = "", ?string $parent_id = null)
 *
 * Class ShipmentCostService
 * @package App\Utils\CMS\ShipmentCost
 */
class FinancialDriverService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_financial_driver_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    public static function defaultRecord($name): FinancialDriverModel
    {
        $financial_driver_model = new FinancialDriverModel();
        $financial_driver_model->setDriverId($name);
        $driver_config = ConfigProvider::getDefaultConfig($name);
        $financial_driver_model->setConfigModel($driver_config);
        return $financial_driver_model;
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function updateRecord(string $driver_id, BaseFinancialConfig $driver_config): FinancialDriverModel
    {
        $financial_driver_model = new FinancialDriverModel();
        $financial_driver_model->setConfigModel($driver_config);
        $financial_driver_model->setDriverId($driver_id);
        FinancialDriverService::setRecord($financial_driver_model);
        return $financial_driver_model;
    }
}
