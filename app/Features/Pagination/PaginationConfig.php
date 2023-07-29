<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/20/17
 * Time: 10:25 PM
 */

namespace App\Features\Pagination;


use App\Common\BaseFeatureConfig;
use App\Helpers\EloquentModelHelper;
use App\Helpers\RequestHelper;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;

/**
 * @method static PaginationSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class PaginationConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = '_pagination';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::SESSION;

    public static function defaultRecord($name): PaginationSettingData
    {
        $default = new PaginationSettingData();
        $default->setModel(EloquentModelHelper::className($name));
        $default->setPage(1);

        return $default;
    }

    public static function initiate($name, $parent_id = null)
    {
        if (self::validateName($name)) {
            RequestHelper::setAttr('page', self::getRecord($name, $parent_id)->getPage());
        }
    }
}
