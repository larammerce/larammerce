<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 8:01 PM
 */

namespace App\Utils\CMS\Setting;

use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Utils\CMS\Enums\DataSourceDriver;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class CMSSettingService {

    private const GLOBAL_KEY="globals";
    private const LOCAL_KEY="locals";

    private static array $CACHED_RECORDS = [];

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
                    Log::error("CMSSettingService.setLocal.DATABASE." . $e->getMessage());
                }
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            $value = serialize($value);
            Log::info("CMSSettingService.setLocal.SESSION.key.{$key}.value.{$value}");
            request()->session()->put($key, $value);
        }
    }

    public static function getGlobal($key, string $driver = DataSourceDriver::DATABASE): ?Setting {
        if (!isset(static::$CACHED_RECORDS[static::GLOBAL_KEY][$key])) {
            if ($driver == DataSourceDriver::DATABASE) {
                try {
                    static::$CACHED_RECORDS[static::GLOBAL_KEY][$key] = Setting::globalData()->systemSettings()->where("key", $key)->firstOrFail();
                } catch (Exception $e) {
                    //Log::error("CMSSettingService.getGlobal.DATABASE." . $e->getMessage());
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
                    //Log::error("CMSSettingService.getLocal.DATABASE." . $e->getMessage());
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = null;
                }
            } else if ($driver == DataSourceDriver::SESSION) {
                $strResult = request()->session()->get($key, false);
                if ($strResult !== false) {
                    $result = new Setting();
                    $result->key = $key;
                    $result->value = $strResult;
                    static::$CACHED_RECORDS[static::LOCAL_KEY][$key] = $result;
                }else{
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
