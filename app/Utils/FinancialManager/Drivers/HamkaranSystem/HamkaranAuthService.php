<?php


namespace App\Utils\FinancialManager\Drivers\HamkaranSystem;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;

/**
 * @method static HamkaranAuthModel getRecord(string $name = "", ?string $parent_id = null)
 */
class HamkaranAuthService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = 'hamkaran_auth_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;
}
