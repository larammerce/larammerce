<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/14/17
 * Time: 7:29 PM
 */

namespace App\Utils\CMS\Setting;


use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\Common\ModelService;
use Exception;
use Illuminate\Support\Str;

abstract class AbstractSettingService
{
    protected static string $KEY_POSTFIX = '_setting';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    protected static function getKey($name, $parent_id = null): string
    {
        return Str::snake(ModelService::className($name)) . static::$KEY_POSTFIX . (($parent_id == null) ? '' : "_{$parent_id}");
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function setRecord(AbstractSettingModel $record, ?string $parent_id = null): void
    {
        if ($record->validate()) {
            if (static::$SETTING_TYPE == SettingType::LOCAL_SETTING)
                SettingService::setLocal(static::getKey($record->getPrimaryKey(), $parent_id), $record, static::$DRIVER);
            else if (static::$SETTING_TYPE == SettingType::GLOBAL_SETTING)
                SettingService::setGlobal(static::getKey($record->getPrimaryKey(), $parent_id), $record, static::$DRIVER);
        } else {
            throw new NotValidSettingRecordException("The record {$record->getPrimaryKey()} is not valid!");
        }
    }

    public static function getRecord(string $name = "", ?string $parent_id = null): ?AbstractSettingModel
    {
        if (static::validateName($name)) {
            $result = null;
            if (static::$SETTING_TYPE == SettingType::LOCAL_SETTING){
                $result = SettingService::getLocal(static::getKey($name, $parent_id), static::$DRIVER);
            }
            else if (static::$SETTING_TYPE == SettingType::GLOBAL_SETTING){
                $result = SettingService::getGlobal(static::getKey($name, $parent_id), static::$DRIVER);
            }

            if ($result == null or $result->data == null) {
                return static::defaultRecord($name);
            }
            return $result->data;
        }
        return null;
    }

    protected static function validateName($name): bool
    {
        return true;
    }

    public static function defaultRecord($name)
    {
        return null;
    }
}
