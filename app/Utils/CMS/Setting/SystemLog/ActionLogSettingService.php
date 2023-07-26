<?php


namespace App\Utils\CMS\Setting\SystemLog;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\AbstractSettingService;

/**
 * @method static ActionLogSettingDataInterface getRecord(string $name = "", ?string $parent_id = null)
 */
class ActionLogSettingService extends AbstractSettingService
{
    protected static string $KEY_POSTFIX = 'action_log_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): ActionLogSettingDataInterface
    {
        return new ActionLogSettingDataInterface();
    }

    public static function isControllerEnabled(string $controller): bool
    {
        $record = self::getRecord();
        $enabled_controllers = $record->getEnabledControllers();
        return in_array($controller,$enabled_controllers);
    }
}
