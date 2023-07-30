<?php


namespace App\Utils\CMS\Setting\SystemUpgrade;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\AbstractSettingService;

/**
 * @method static SystemUpgradeSettingDataInterface getRecord(string $name = "", ?string $parent_id = null)
 */
class SystemUpgradeSettingService extends AbstractSettingService {
    protected static string $KEY_POSTFIX = 'system_upgrade_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): SystemUpgradeSettingDataInterface {
        return new SystemUpgradeSettingDataInterface(
            "git@github.com:larammerce/larammerce.git",
            "production",
            "git@github.com:larammerce/larammerce-base-theme.git",
            "master"
        );
    }
}
