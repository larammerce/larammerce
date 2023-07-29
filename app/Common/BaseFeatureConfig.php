<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 8/14/17
 * Time: 7:29 PM
 */

namespace App\Common;


use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Utils\CMS\Enums\DataSourceDriver;
use App\Utils\CMS\Enums\SettingType;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

abstract class BaseFeatureConfig
{
    protected static string $KEY_POSTFIX = '_setting';
    protected static int $SETTING_TYPE = SettingType::LOCAL_SETTING;
    protected static string $DRIVER = DataSourceDriver::DATABASE;

    private const GLOBAL_KEY = "globals";
    private const LOCAL_KEY = "locals";

    private static array $CACHED_RECORDS = [];

    protected static function getKey($name, $parent_id = null): string {
        return Str::snake(\App\Helpers\EloquentModelHelper::className($name)) . static::$KEY_POSTFIX . (($parent_id == null) ? '' : "_{$parent_id}");
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function setRecord(SettingDataInterface $record, ?string $parent_id = null): void {
        if ($record->validate()) {
            if (static::$SETTING_TYPE == SettingType::LOCAL_SETTING)
                BaseFeatureConfig::setLocal(static::getKey($record->getPrimaryKey(), $parent_id), $record, static::$DRIVER);
            else if (static::$SETTING_TYPE == SettingType::GLOBAL_SETTING)
                BaseFeatureConfig::setGlobal(static::getKey($record->getPrimaryKey(), $parent_id), $record, static::$DRIVER);
        } else {
            throw new NotValidSettingRecordException("The record {$record->getPrimaryKey()} is not valid!");
        }
    }

    public static function getRecord(string $name = "", ?string $parent_id = null): ?SettingDataInterface {
        if (static::validateName($name)) {
            $result = null;
            if (static::$SETTING_TYPE == SettingType::LOCAL_SETTING) {
                $result = BaseFeatureConfig::getLocal(static::getKey($name, $parent_id), static::$DRIVER);
            } else if (static::$SETTING_TYPE == SettingType::GLOBAL_SETTING) {
                $result = BaseFeatureConfig::getGlobal(static::getKey($name, $parent_id), static::$DRIVER);
            }

            if ($result == null or $result->data == null) {
                return static::defaultRecord($name);
            }
            return $result->data;
        }
        return null;
    }

    protected static function validateName($name): bool {
        return true;
    }

    public static function defaultRecord($name) {
        return null;
    }

    public static function setGlobal(string $key, SettingDataInterface $value, string $driver = DataSourceDriver::DATABASE): void {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getGlobal($key);
            $value = serialize($value);
            if ($setting) {
                static::$CACHED_RECORDS[static::GLOBAL_KEY][$key] = $setting->update(compact('value'));
            } else {
                static::$CACHED_RECORDS[static::GLOBAL_KEY][$key] = self::create($key, $value);
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
    }

    public static function setLocal(string $key, SettingDataInterface $value, string $driver = DataSourceDriver::DATABASE): void {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getLocal($key);
            $value = serialize($value);
            if ($setting)
                static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = $setting->update(compact('value'));
            else {
                try {
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = self::create($key, $value, Auth::user()->id);
                } catch (Exception $e) {
                    Log::error("FeatureConfigService.setLocal.DATABASE." . $e->getMessage());
                }
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            $value = serialize($value);
            Log::info("FeatureConfigService.setLocal.SESSION.key.{$key}.value.{$value}");
            request()->session()->put($key, $value);
        }
    }

    public static function getGlobal($key, string $driver = DataSourceDriver::DATABASE): ?Setting {
        if (!isset(static::$CACHED_RECORDS[static::GLOBAL_KEY][$key])) {
            if ($driver == DataSourceDriver::DATABASE) {
                try {
                    static::$CACHED_RECORDS[static::GLOBAL_KEY][$key] = Setting::globalData()->systemSettings()->where("key", $key)->firstOrFail();
                } catch (Exception $e) {
                    //Log::error("FeatureConfigService.getGlobal.DATABASE." . $e->getMessage());
                    static::$CACHED_RECORDS[static::GLOBAL_KEY][$key] = null;
                }
            } else if ($driver == DataSourceDriver::SESSION) {
                static::$CACHED_RECORDS[static::GLOBAL_KEY][$key] = null;
                die('session can not be global ! :) go read more about sessions :D');
            }
        }
        return static::$CACHED_RECORDS[static::GLOBAL_KEY][$key];
    }

    public static function getLocal($key, string $driver = DataSourceDriver::DATABASE): ?Setting {
        if (!isset(static::$CACHED_RECORDS[static::LOCAL_KEY][$key])) {
            if ($driver == DataSourceDriver::DATABASE) {
                try {
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = Setting::personalItems()->systemSettings()->where("key", $key)->firstOrFail();
                } catch (Exception $e) {
                    Setting::personalItems()->systemSettings()->where("key", $key)->delete();
                    //Log::error("FeatureConfigService.getLocal.DATABASE." . $e->getMessage());
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = null;
                }
            } else if ($driver == DataSourceDriver::SESSION) {
                $strResult = request()->session()->get($key, false);
                if ($strResult !== false) {
                    $result = new Setting();
                    $result->key = $key;
                    $result->value = $strResult;
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = $result;
                } else {
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = null;
                }
            }
        }
        return static::$CACHED_RECORDS[static::LOCAL_KEY][$key];
    }

    public static function get($key, string $driver = DataSourceDriver::DATABASE): ?Setting {
        return self::getLocal($key, $driver) ?? self::getGlobal($key, $driver);
    }

    public static function deleteGlobal(string $key, string $driver = DataSourceDriver::DATABASE): void {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getGlobal($key, $driver);
            $setting?->delete();
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
    }

    public static function deleteLocal(string $key, string $driver = DataSourceDriver::DATABASE): void {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getLocal($key, $driver);
            $setting?->delete();
        } else if ($driver == DataSourceDriver::SESSION) {
            request()->session()->forget($key);
        }
    }
}
