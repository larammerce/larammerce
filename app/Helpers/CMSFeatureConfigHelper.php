<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 8/9/17
 * Time: 8:01 PM
 */

namespace App\Helpers;

use App\Interfaces\Repositories\SettingRepositoryInterface;
use App\Interfaces\SettingDataInterface;
use App\Models\Setting;
use App\Models\User;
use App\Utils\CMS\Enums\DataSourceDriver;
use Illuminate\Support\Facades\Auth;


class CMSFeatureConfigHelper {


    public static function getGlobal($key, string $driver = DataSourceDriver::DATABASE): ?Setting {
        if ($driver == DataSourceDriver::DATABASE) {
            /** @var SettingRepositoryInterface $setting_repository */
            $setting_repository = app(SettingRepositoryInterface::class);
            return $setting_repository->findGlobalSystemSetting($key);
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
        return null;
    }

    public static function setGlobal(string $key, SettingDataInterface $value, string $driver = DataSourceDriver::DATABASE): void {
        if ($driver == DataSourceDriver::DATABASE) {
            /** @var SettingRepositoryInterface $setting_repository */
            $setting_repository = app(SettingRepositoryInterface::class);
            $setting = self::getGlobal($key);
            if ($setting) {
                $setting_repository->updateGlobalSystemSettingWithDataInterface($setting, $key, $value);
            } else {
                $setting_repository->createGlobalSystemSettingWithDataInterface($key, $value);
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            die('session can not be global ! :) go read more about sessions :D');
        }
    }

    public static function getLocal($key, string $driver = DataSourceDriver::DATABASE): ?Setting {

        if ($driver == DataSourceDriver::DATABASE) {
            /** @var SettingRepositoryInterface $setting_repository */
            $setting_repository = app(SettingRepositoryInterface::class);
            /** @var User $user */
            $user = Auth::user();
            return $setting_repository->findPersonalSystemSetting($key, $user);
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

    public static function setLocal(string $key, SettingDataInterface $data, string $driver = DataSourceDriver::DATABASE): void {
        if ($driver == DataSourceDriver::DATABASE) {
            /** @var SettingRepositoryInterface $setting_repository */
            $setting_repository = app(SettingRepositoryInterface::class);
            /** @var User $user */
            $user = Auth::user();
            $setting = self::getLocal($key);
            if ($setting) {
                $setting_repository->updatePersonalSystemSettingWithDataInterface($setting, $key, $data, $user);
            } else {
                $setting_repository->createPersonalSystemSettingWithDataInterface($key, $data, $user);
            }
        } else if ($driver == DataSourceDriver::SESSION) {
            $value = serialize($data);
            request()->session()->put($key, $value);
        }
    }

    public static function get($key, string $driver = DataSourceDriver::DATABASE): ?Setting {
        return self::getLocal($key, $driver) ?? self::getGlobal($key, $driver);
    }
}
