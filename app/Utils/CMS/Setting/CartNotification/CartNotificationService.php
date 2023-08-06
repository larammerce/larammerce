<?php


namespace App\Utils\CMS\Setting\CartNotification;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;

/**
 * @method static CartNotificationModel getRecord(string $name = "", ?string $parent_id = null)
 */
class CartNotificationService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = 'cart_notification_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): CartNotificationModel
    {
        return new CartNotificationModel();
    }
}
