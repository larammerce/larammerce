<?php

namespace App\Utils\CMS\Setting\CRMDriver;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\CRMManager\ConfigProvider;
use App\Utils\CRMManager\Exceptions\CRMDriverInvalidConfigurationException;

/**
 * @method static CRMDriverModel getRecord(string $name = "", ?string $parent_id = null)
 */
class CRMDriverService extends BaseCMSConfigManager {
    protected static string $KEY_POSTFIX = '_crm_driver_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    /**
     * @throws CRMDriverInvalidConfigurationException
     */
    public static function defaultRecord($name): CRMDriverModel {
        $crm_driver_model = new CRMDriverModel();
        $crm_driver_model->setDriverId($name);
        $driver_config = ConfigProvider::getDefaultConfig($name);
        $crm_driver_model->setConfigModel(serialize($driver_config));
        return $crm_driver_model;
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function updateRecord(string $driver_id, string $driver_config): CRMDriverModel {
        $crm_driver_model = new CRMDriverModel();
        $crm_driver_model->setConfigModel($driver_config);
        $crm_driver_model->setDriverId($driver_id);
        CRMDriverService::setRecord($crm_driver_model);
        return $crm_driver_model;
    }
}
