<?php


namespace App\Utils\CMS\Setting\Survey;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Setting\AbstractSettingService;

/**
 * @method static SurveyDataInterface getRecord(string $name = "", ?string $parent_id = null)
 */
class SurveyService extends AbstractSettingService
{
    protected static string $KEY_POSTFIX = 'survey_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): SurveyDataInterface
    {
        return new SurveyDataInterface();
    }
}
