<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/12/17
 * Time: 3:29 PM
 */

namespace App\Features\Layout;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\LayoutType;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\Common\ModelService;

/**
 * @method static LayoutSettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class LayoutConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = '_layout';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    protected static function validateName($name): bool
    {
        return ModelService::isValidModel($name);
    }

    public static function getLayoutMethods(): array
    {
        return [
            [
                'icon' => 'fa-list',
                'name' => 'general.layout.list',
                'method' => LayoutType::LIST_ITEMS,
            ],
            [
                'icon' => 'fa-th',
                'name' => 'general.layout.grid',
                'method' => LayoutType::GRID_ITEMS,
            ]
        ];
    }

    public static function defaultRecord($name): LayoutSettingData
    {
        $default = new LayoutSettingData();
        $default->setModel(ModelService::className($name));
        $default->setMethod(LayoutType::LIST_ITEMS);

        return $default;
    }
}
