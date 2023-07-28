<?php


namespace App\Features\CartNotification;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;

/**
 * @method static CartNotificationSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class CartNotificationConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = 'cart_notification_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): CartNotificationSettingData
    {
        return new CartNotificationSettingData();
    }
}
