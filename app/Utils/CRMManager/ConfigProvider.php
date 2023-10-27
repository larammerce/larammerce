<?php

namespace App\Utils\CRMManager;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\CRMDriver\CRMDriverService;
use App\Utils\CRMManager\Drivers\Sarv\Config as SarvConfig;
use App\Utils\CRMManager\Exceptions\CRMDriverInvalidConfigurationException;
use App\Utils\CRMManager\Models\BaseCRMConfig;
use App\Utils\Reflection\AnnotationBadKeyException;
use App\Utils\Reflection\AnnotationBadScopeException;
use App\Utils\Reflection\AnnotationNotFoundException;
use App\Utils\Reflection\AnnotationSyntaxException;
use ReflectionException;

class ConfigProvider {
    static array $CACHED_DATA = [];

    /**
     * @throws CRMDriverInvalidConfigurationException
     */
    public static function getDefaultConfig(string $driver_id): BaseCRMConfig {
        return Factory::driver($driver_id)->getDefaultConfig();
    }

    public static function getConfig(string $driver_id): BaseCRMConfig|SarvConfig {
        if (count(self::$CACHED_DATA) == 0 or !array_key_exists($driver_id, self::$CACHED_DATA)) {
            $CRM_driver_setting_record = CRMDriverService::getRecord($driver_id);
            self::$CACHED_DATA[$driver_id] = unserialize($CRM_driver_setting_record->getConfigModel());
        }
        return self::$CACHED_DATA[$driver_id];
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function setConfig(string $driver_id, BaseCRMConfig $config): void {
        self::$CACHED_DATA[$driver_id] = $config;
        CRMDriverService::updateRecord($driver_id, serialize($config));
    }

    public static function getAll(): array {
        $result = [];
        $drivers = Kernel::$drivers;
        foreach ($drivers as $driver_id => $driver_class) {
            if (Provider::hasDriver($driver_id)) {
                $driver_config = self::getConfig($driver_id);
                $result[$driver_id] = $driver_config;
            }
        }
        return $result;
    }

    /**
     * @param BaseCRMConfig[] $drivers
     * @throws NotValidSettingRecordException
     * @throws CRMDriverInvalidConfigurationException
     */
    public static function setAll(array $drivers) {
        $last_enabled_driver_id = null;
        foreach ($drivers as $driver_id => $driver_config_data) {
            if (Provider::hasDriver($driver_id)) {
                $driver_config = self::getConfig($driver_id);
                $was_enabled = $driver_config->is_enabled;
                foreach ($driver_config_data as $key => $value) {
                    $driver_config->$key = $value;
                }
                CRMDriverService::updateRecord($driver_id, serialize($driver_config));
                self::$CACHED_DATA[$driver_id] = $driver_config;
                if (!$was_enabled and $driver_config->is_enabled)
                    $last_enabled_driver_id = $driver_id;
            } else
                throw new CRMDriverInvalidConfigurationException();
        }
        if ($last_enabled_driver_id != null)
            self::resetEnabledDrivers(array_diff_key($drivers, [$last_enabled_driver_id => 0]));
    }

    /**
     * @throws NotValidSettingRecordException
     */
    private static function resetEnabledDrivers(array $other_drivers): void {
        foreach ($other_drivers as $other_driver_id => $data) {
            $other_driver_config = self::getConfig($other_driver_id);
            $other_driver_config->is_enabled = false;
            CRMDriverService::updateRecord($other_driver_id, serialize($other_driver_config));
            self::$CACHED_DATA[$other_driver_id] = $other_driver_config;
        }
    }

    /**
     * @throws AnnotationNotFoundException
     * @throws AnnotationSyntaxException
     * @throws CRMDriverInvalidConfigurationException
     * @throws AnnotationBadScopeException
     * @throws AnnotationBadKeyException
     * @throws ReflectionException
     */
    public static function getRules($drivers): array {
        if ($drivers === null)
            return [];

        $rules = [];
        foreach ($drivers as $driver_id => $inputs) {
            $driver_config = self::getDefaultConfig($driver_id);
            foreach ($inputs as $input_key => $value)
                $rules[$input_key] = $driver_config->getInputRule($input_key);
        }
        return array_filter($rules);
    }
}
