<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/20/17
 * Time: 10:25 PM
 */

namespace App\Utils\CMS\Setting\Pagination;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use App\Utils\Common\ModelService;
use App\Utils\Common\RequestService;

/**
 * @method static PaginationModel getRecord(string $name = "", ?string $parent_id = null)
 */
class PaginationService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = '_pagination';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::SESSION;

    public static function defaultRecord($name): PaginationModel
    {
        $default = new PaginationModel();
        $default->setModel(ModelService::className($name));
        $default->setPage(1);

        return $default;
    }

    public static function initiate($name, $parent_id = null)
    {
        if (self::validateName($name)) {
            RequestService::setAttr('page', self::getRecord($name, $parent_id)->getPage());
        }
    }
}
