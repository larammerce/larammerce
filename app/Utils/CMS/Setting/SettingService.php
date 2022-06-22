<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 8:01 PM
 */

namespace App\Utils\CMS\Setting;

use App\Models\Setting;
use App\Utils\CMS\Enums\DataSourceDriver;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class SettingService
{

    private static function create(string $key, string $value, ?int $user_id = null): void
    {
        Setting::create([
            "key" => $key,
            "value" => $value,
            "user_id" => $user_id,
            "is_system_setting" => true
        ]);
    }

    public static function setGlobal(string $key, AbstractSettingModel $value, string $driver = DataSourceDriver::DATABASE): void
    {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getGlobal($key);
            $value = serialize($value);
            if ($setting){
                $setting->update(compact('value'));
            }
            else{
                self::create($key, $value);
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
    }

    public static function setLocal(string $key, AbstractSettingModel $value, string $driver = DataSourceDriver::DATABASE): void
    {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getLocal($key);
            $value = serialize($value);
            if ($setting)
                $setting->update(compact('value'));
            else {
                try {
                    self::create($key, $value, Auth::user()->id);
                } catch (Exception $e) {
                    Log::error("SettingService.setLocal.DATABASE." . $e->getMessage());
                }
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            $value = serialize($value);
            Log::info("SettingService.setLocal.SESSION.key.{$key}.value.{$value}");
            request()->session()->put($key, $value);
        }
    }

    public static function getGlobal($key, string $driver = DataSourceDriver::DATABASE): ?Setting
    {
        if ($driver == DataSourceDriver::DATABASE) {
            try {
                return Setting::globalData()->systemSettings()->where("key", $key)->firstOrFail();
            } catch (Exception $e) {
                //Log::error("SettingService.getGlobal.DATABASE." . $e->getMessage());
                return null;
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
        return null;
    }

    public static function getLocal($key, string $driver = DataSourceDriver::DATABASE): ?Setting
    {
        if ($driver == DataSourceDriver::DATABASE) {
            try {
                return Setting::localData()->systemSettings()->where("key", $key)->firstOrFail();
            } catch (Exception $e) {
                Setting::localData()->systemSettings()->where("key", $key)->delete();
                //Log::error("SettingService.getLocal.DATABASE." . $e->getMessage());
                return null;
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            $strResult = request()->session()->get($key, false);
            if ($strResult !== false) {
                $result = new Setting();
                $result->key = $key;
                $result->value = $strResult;
                return $result;
            }
        }
        return null;
    }

    public static function get($key, string $driver = DataSourceDriver::DATABASE): ?Setting
    {
        return self::getLocal($key, $driver) ?? self::getGlobal($key, $driver);
    }

    public static function deleteGlobal(string $key, string $driver = DataSourceDriver::DATABASE): void
    {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getGlobal($key, $driver);
            $setting?->delete();
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
    }

    public static function deleteLocal(string $key, string $driver = DataSourceDriver::DATABASE): void
    {
        if ($driver == DataSourceDriver::DATABASE) {
            $setting = self::getLocal($key, $driver);
            $setting?->delete();
        } else if ($driver == DataSourceDriver::SESSION) {
            request()->session()->forget($key);
        }
    }
}
