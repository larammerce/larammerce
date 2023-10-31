<?php

namespace App\Utils\FinancialManager;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\FinancialDriver\FinancialDriverService;
use App\Utils\FinancialManager\Drivers\Local\Config;
use App\Utils\FinancialManager\Exceptions\FinancialDriverInvalidConfigurationException;
use App\Utils\FinancialManager\Models\BaseFinancialConfig;
use App\Utils\Reflection\AnnotationBadKeyException;
use App\Utils\Reflection\AnnotationBadScopeException;
use App\Utils\Reflection\AnnotationNotFoundException;
use App\Utils\Reflection\AnnotationSyntaxException;
use ReflectionException;

class ConfigProvider {
    static array $CACHED_DATA = [];

    /**
     * @throws FinancialDriverInvalidConfigurationException
     */
    public static function getDefaultConfig(string $driver_id): BaseFinancialConfig {
        return Factory::driver($driver_id)->getDefaultConfig();
    }

    public static function getConfig(string $driver_id): BaseFinancialConfig {
        if (count(self::$CACHED_DATA) == 0 or !array_key_exists($driver_id, self::$CACHED_DATA)) {
            $payment_driver_setting_record = FinancialDriverService::getRecord($driver_id);
            self::$CACHED_DATA[$driver_id] = $payment_driver_setting_record->getConfigModel();
        }
        return self::$CACHED_DATA[$driver_id];
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
     * @param BaseFinancialConfig[] $drivers
     * @throws NotValidSettingRecordException
     * @throws FinancialDriverInvalidConfigurationException
     */
    public static function setAll(array $drivers): void {
        $last_enabled_driver_id = null;
        foreach ($drivers as $driver_id => $driver_config_data) {
            if (Provider::hasDriver($driver_id)) {
                $driver_config = self::getConfig($driver_id);
                $was_enabled = $driver_config->is_enabled;
                foreach ($driver_config_data as $key => $value) {
                    $driver_config->$key = $value;
                }
                FinancialDriverService::updateRecord($driver_id, $driver_config);
                self::$CACHED_DATA[$driver_id] = $driver_config;
                if (!$was_enabled and $driver_config->is_enabled)
                    $last_enabled_driver_id = $driver_id;
            } else
                throw new FinancialDriverInvalidConfigurationException();
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
            FinancialDriverService::updateRecord($other_driver_id, $other_driver_config);
            self::$CACHED_DATA[$other_driver_id] = $other_driver_config;
        }
    }

    /**
     * @throws AnnotationNotFoundException
     * @throws AnnotationSyntaxException
     * @throws FinancialDriverInvalidConfigurationException
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

    public static function isTaxAddedToPriceByDefault(): bool {
        $financial_driver = Provider::getEnabledDriver();
        $tax_added_to_price = true;
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
            $tax_added_to_price = $config->tax_added_to_price;
        }
        return $tax_added_to_price;
    }

    public static function shouldUsePerProductTaxConfig(): bool {
        $financial_driver = Provider::getEnabledDriver();
        $use_per_product_config = false;
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
            $use_per_product_config = $config->use_per_product_config;
        }
        return $use_per_product_config;
    }

    public static function getDefaultTaxPercentage(): int {
        $financial_driver = Provider::getEnabledDriver();
        $tax_percentage = 0;
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
            $tax_percentage = $config->tax_percentage;
        }
        return $tax_percentage;
    }

    public static function getDefaultTollPercentage(): int {
        $financial_driver = Provider::getEnabledDriver();
        $toll_percentage = 0;
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
            $toll_percentage = $config->toll_percentage;
        }
        return $toll_percentage;
    }

    public static function getInvoiceTaxHeading(): string {
        $financial_driver = Provider::getEnabledDriver();
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
        } else {
            $config = new Config();
        }
        return $config->invoice_tax_heading;
    }

    public static function shouldShowTaxPercentageInInvoiceHeading(): bool {
        $financial_driver = Provider::getEnabledDriver();
        $show_tax_percentage = false;
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
            $show_tax_percentage = $config->invoice_show_tax_percentage;
        }
        return $show_tax_percentage;
    }

    public static function isNationalCodeRequired(): bool {
        $financial_driver = Provider::getEnabledDriver();
        $is_national_code_required = true;
        if (strlen($financial_driver) > 0 and Provider::hasDriver($financial_driver)) {
            $config = self::getConfig($financial_driver);
            $is_national_code_required = $config->is_national_code_required;
        }
        return $is_national_code_required;
    }
}
