<?php


namespace App\Utils\CMS\Setting\PaymentDriver;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;

/**
 * @method static PaymentDriverModel getRecord(string $name = "", ?string $parent_id = null)
 */
class PaymentDriverService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_payment_driver_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    /**
     * @throws PaymentInvalidDriverException
     */
    public static function defaultRecord($name): PaymentDriverModel
    {
        $payment_driver_model = new PaymentDriverModel();
        $payment_driver_model->setDriverId($name);
        $driver_config = ConfigProvider::getDefaultConfig($name);
        $payment_driver_model->setConfigModel(serialize($driver_config));
        return $payment_driver_model;
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function updateRecord(string $driver_id, string $driver_config): PaymentDriverModel
    {
        $payment_driver_model = new PaymentDriverModel();
        $payment_driver_model->setConfigModel($driver_config);
        $payment_driver_model->setDriverId($driver_id);
        PaymentDriverService::setRecord($payment_driver_model);
        return $payment_driver_model;
    }
}
