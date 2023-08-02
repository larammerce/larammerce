<?php


namespace App\Utils\CMS\Setting\Excel;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\BaseCMSConfigManager;
use Illuminate\Support\Facades\Log;

/**
 * @method static ExcelCacheModel getRecord(string $name = "", ?string $parent_id = null)
 */
class ExcelCacheService extends BaseCMSConfigManager
{
    protected static string $KEY_POSTFIX = 'excel_cache_config';
    protected static int $SETTING_TYPE = SettingType::GLOBAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    public static function defaultRecord($name): ExcelCacheModel
    {
        return new ExcelCacheModel();
    }

    public static function getAttributes(string $model)
    {
        $record = self::getRecord();
        $models = $record->getModels();
        if (in_array($model, $models)) {
            $model_attributes = $record->getModelAttributes();;
            $attributes = $model_attributes[$model];
        } else {
            $attributes = [];
        }
        return $attributes;
    }

    public static function getRelations(string $model)
    {
        $record = self::getRecord();
        $models = $record->getModels();
        if (in_array($model, $models)) {
            $model_relations = $record->getModelRelations();;
            $relations = $model_relations[$model];
        } else {
            $relations = [];
        }
        return $relations;
    }

    public static function update(string $model, array $model_attributes = [], array $model_relations = []): bool
    {
        $record = self::getRecord();
        $record_models = $record->getModels();
        $record_model_attributes = $record->getModelAttributes();
        $record_model_relations = $record->getModelRelations();

        if (!in_array($model, $record_models)) {
            $record_models[] = $model;
        }

        $record_model_attributes[$model] = $model_attributes;
        $record_model_relations[$model] = $model_relations;

        $record->setModels($record_models);
        $record->setModelAttributes($record_model_attributes);
        $record->setModelRelations($record_model_relations);
        try {
            self::setRecord($record);
            return true;
        } catch (NotValidSettingRecordException $e) {
            Log::error("ExcelCacheService.update.NotValidSettingRecordException." . $e->getMessage());
            return false;
        }
    }
}
