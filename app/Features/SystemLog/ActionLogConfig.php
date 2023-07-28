<?php


namespace App\Features\SystemLog;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;

/**
 * @method static ActionLogSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class ActionLogConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = 'action_log_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): ActionLogSettingData
    {
        return new ActionLogSettingData();
    }

    public static function isControllerEnabled(string $controller): bool
    {
        $record = self::getRecord();
        $enabled_controllers = $record->getEnabledControllers();
        return in_array($controller,$enabled_controllers);
    }
}
