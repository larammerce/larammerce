<?php

namespace App\Utils\CMS\Setting\ProductWatermark;

use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\BaseCMSConfigManager;

/**
 * @method static ProductWatermarkSettingModel getRecord(string $name = "", ?string $parent_id = null)
 */
class ProductWatermarkSettingService extends BaseCMSConfigManager {
    protected static string $KEY_POSTFIX = '_product_watermark';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): ProductWatermarkSettingModel {
        $default = new ProductWatermarkSettingModel();
        $default->setWatermarkImage("");
        $default->regenerateUUID();
        $default->setWatermarkPosition("bottom_right");
        $default->setWatermarkSizePercentage(10);
        $default->activate();

        return $default;
    }
}
