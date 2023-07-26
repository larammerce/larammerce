<?php

namespace App\Utils\PaymentManager;

use App\Interfaces\AttachedFileInterface;
use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\PaymentDriver\PaymentDriverService;
use App\Utils\PaymentManager\Drivers\Pep\Config;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use App\Utils\PaymentManager\Models\BasePaymentConfig;
use Illuminate\Http\UploadedFile;

class ConfigProvider
{
    const MAX_TRANSACTION = 500000000;
    static array $CACHED_DATA = [];

    /**
     * @throws PaymentInvalidDriverException
     */
    public static function getDefaultConfig(string $driver_id): BasePaymentConfig
    {
        return Factory::driver($driver_id)->getDefaultConfig();
    }

    public static function getConfig(string $driver_id): BasePaymentConfig|Config
    {
        if (count(self::$CACHED_DATA) == 0 or !array_key_exists($driver_id, self::$CACHED_DATA))
        {
            $payment_driver_setting_record = PaymentDriverService::getRecord($driver_id);
            self::$CACHED_DATA[$driver_id] = unserialize($payment_driver_setting_record->getConfigModel());
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
     * @param BasePaymentConfig[] $drivers
     * @throws NotValidSettingRecordException
     * @throws PaymentInvalidDriverException
     */
    public static function setAll(array $drivers)
    {
        $last_default_driver_id = null;
        foreach ($drivers as $driver_id => $driver_config_data) {
            if (Provider::hasDriver($driver_id)) {
                $driver_config = self::getConfig($driver_id);
                $was_default = $driver_config->is_default;
                foreach ($driver_config_data as $key => $value) {
                    if ($driver_config instanceof AttachedFileInterface
                    and $value instanceof UploadedFile)
                        $driver_config->setFilePath([$key => $value]);
                    else
                        $driver_config->$key = $value;
                }

                if (!$was_default and $driver_config->is_default) {
                    $last_default_driver_id = $driver_id;
                    $driver_config->is_enabled = true;
                }
                PaymentDriverService::updateRecord($driver_id, serialize($driver_config));
                self::$CACHED_DATA[$driver_id] = $driver_config;
            } else
                throw new PaymentInvalidDriverException();
        }
        if ($last_default_driver_id != null)
            self::resetDefaultDrivers(array_diff_key($drivers, [$last_default_driver_id => 0]));
    }

    /**
     * @throws NotValidSettingRecordException
     */
    private static function resetDefaultDrivers(array $other_drivers)
    {
        foreach ($other_drivers as $other_driver_id => $data) {
            $other_driver_config = self::getConfig($other_driver_id);
            $other_driver_config->is_default = false;
            PaymentDriverService::updateRecord($other_driver_id, serialize($other_driver_config));
            self::$CACHED_DATA[$other_driver_id] = $other_driver_config;
        }
    }

    /**
     * @throws NotValidSettingRecordException
     */
    public static function removeFile(string $driver_id)
    {
        if (Provider::hasDriver($driver_id)) {
            $driver_config = self::getConfig($driver_id);
            if ($driver_config instanceof AttachedFileInterface) {
                $driver_config->removeFile();
                PaymentDriverService::updateRecord($driver_id, serialize($driver_config));
            }
        }
    }

    /**
     * @throws \App\Utils\Reflection\AnnotationNotFoundException
     * @throws \App\Utils\Reflection\AnnotationSyntaxException
     * @throws PaymentInvalidDriverException
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
        return $rules;
    }

    public static function getMaxTransactionAmount(): int {
        return intval(env("SITE_MAX_TRANSACTION_AMOUNT", static::MAX_TRANSACTION));
    }
}
