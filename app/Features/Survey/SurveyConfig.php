<?php


namespace App\Features\Survey;


use App\Common\BaseFeatureConfig;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;

/**
 * @method static SurveySettingData getRecord(string $name = "", ?string $parent_id = null)
 */
class SurveyConfig extends BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = 'survey_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): SurveySettingData
    {
        return new SurveySettingData();
    }
}
