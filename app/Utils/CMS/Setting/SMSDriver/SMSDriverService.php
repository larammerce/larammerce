<?php

namespace App\Utils\CMS\Setting\SMSDriver;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\SMSManager\ConfigProvider;
use App\Utils\SMSManager\Exceptions\SMSDriverInvalidConfigurationException;
use App\Utils\SMSManager\Exceptions\SMSDriverNotConfiguredException;

/**
 * @method static SMSDriverModel getRecord(string $name = "", ?string $parent_id = null)
 */
class SMSDriverService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_sms_driver_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    /**
     * @throws SMSDriverInvalidConfigurationException
     */
    public static function defaultRecord($name): SMSDriverModel
    {
        $sms_driver_model = new SMSDriverModel();
        $sms_driver_model->setDriverId($name);
        $driver_config = ConfigProvider::getDefaultConfig($name);
        $sms_driver_model->setConfigModel(serialize($driver_config));
        return $sms_driver_model;
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function updateRecord(string $driver_id, string $driver_config): SMSDriverModel
    {
        $sms_driver_model = new SMSDriverModel();
        $sms_driver_model->setConfigModel($driver_config);
        $sms_driver_model->setDriverId($driver_id);
        SMSDriverService::setRecord($sms_driver_model);
        return $sms_driver_model;
    }
}
