<?php

namespace App\Utils\SMSManager;

use App\Features\SMSDriver\SMSDriverConfig;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\SMSManager\Drivers\Farapayamak\Config as FaraparamakConfig;
use App\Utils\SMSManager\Drivers\File\Config as FileConfig;
use App\Utils\SMSManager\Drivers\Kavenegar\Config as KavenegarConfig;
use App\Utils\SMSManager\Exceptions\SMSDriverInvalidConfigurationException;
use App\Utils\SMSManager\Models\BaseSMSConfig;

class ConfigProvider
{
    static array $CACHED_DATA = [];

    /**
     * @throws SMSDriverInvalidConfigurationException
     */
    public static function getDefaultConfig(string $driver_id): BaseSMSConfig
    {
        return Factory::driver($driver_id)->getDefaultConfig();
    }

    public static function getConfig(string $driver_id): BaseSMSConfig|FileConfig|FaraparamakConfig|KavenegarConfig
    {
        if (count(self::$CACHED_DATA) == 0 or !array_key_exists($driver_id, self::$CACHED_DATA))
        {
            $sms_driver_setting_record = SMSDriverConfig::getRecord($driver_id);
            self::$CACHED_DATA[$driver_id] = unserialize($sms_driver_setting_record->getConfigModel());
        }
        return self::$CACHED_DATA[$driver_id];
    }

    public static function getAll(): array
    {
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
     * @param BaseSMSConfig[] $drivers
     * @throws NotValidSettingRecordException
     * @throws SMSDriverInvalidConfigurationException
     */
    public static function setAll(array $drivers)
    {
        $last_enabled_driver_id = null;
        foreach ($drivers as $driver_id => $driver_config_data) {
            if (Provider::hasDriver($driver_id)) {
                $driver_config = self::getConfig($driver_id);
                $was_enabled = $driver_config->is_enabled;
                foreach ($driver_config_data as $key => $value) {
                    $driver_config->$key = $value;
                }
                SMSDriverConfig::updateRecord($driver_id, serialize($driver_config));
                self::$CACHED_DATA[$driver_id] = $driver_config;
                if (!$was_enabled and $driver_config->is_enabled)
                    $last_enabled_driver_id = $driver_id;
            } else
                throw new SMSDriverInvalidConfigurationException();
        }
        if ($last_enabled_driver_id != null)
            self::resetEnabledDrivers(array_diff_key($drivers, [$last_enabled_driver_id => 0]));
    }

    /**
     * @throws NotValidSettingRecordException
     */
    private static function resetEnabledDrivers(array $other_drivers)
    {
        foreach ($other_drivers as $other_driver_id => $data)
        {
            $other_driver_config = self::getConfig($other_driver_id);
            $other_driver_config->is_enabled = false;
            SMSDriverConfig::updateRecord($other_driver_id, serialize($other_driver_config));
            self::$CACHED_DATA[$other_driver_id] = $other_driver_config;
        }
    }

    /**
     * @throws \App\Utils\Reflection\AnnotationNotFoundException
     * @throws \App\Utils\Reflection\AnnotationSyntaxException
     * @throws SMSDriverInvalidConfigurationException
     * @throws \App\Utils\Reflection\AnnotationBadScopeException
     * @throws \App\Utils\Reflection\AnnotationBadKeyException
     * @throws \ReflectionException
     */
    public static function getRules($drivers): array
    {
        $rules = [];
        foreach ($drivers as $driver_id => $inputs) {
            $driver_config = self::getDefaultConfig($driver_id);
            foreach ($inputs as $input_key => $value)
                $rules[$input_key] = $driver_config->getInputRule($input_key);
        }
        return array_filter($rules);
    }
}
