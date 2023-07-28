<?php


namespace App\Features\SystemUpgrade;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;

/**
 * @method static SystemUpgradeSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class SystemUpgradeConfig extends BaseFeatureConfig {
    protected static string $KEY_POSTFIX = 'system_upgrade_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): SystemUpgradeSettingData {
        return new SystemUpgradeSettingData(
            "git@github.com:larammerce/larammerce.git",
            "git@github.com:larammerce/larammerce-base-theme.git"
        );
    }
}
