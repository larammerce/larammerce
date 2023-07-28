<?php


namespace App\Features\PaymentDriver;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;

/**
 * @method static PaymentDriverSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class PaymentDriverConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = '_payment_driver_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    /**
     * @throws PaymentInvalidDriverException
     */
    public static function defaultRecord($name): PaymentDriverSettingData
    {
        $payment_driver_model = new PaymentDriverSettingData();
        $payment_driver_model->setDriverId($name);
        $driver_config = ConfigProvider::getDefaultConfig($name);
        $payment_driver_model->setConfigModel(serialize($driver_config));
        return $payment_driver_model;
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function updateRecord(string $driver_id, string $driver_config): PaymentDriverSettingData
    {
        $payment_driver_model = new PaymentDriverSettingData();
        $payment_driver_model->setConfigModel($driver_config);
        $payment_driver_model->setDriverId($driver_id);
        PaymentDriverConfig::setRecord($payment_driver_model);
        return $payment_driver_model;
    }
}
