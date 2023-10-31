<?php

namespace App\Utils\SMSManager;

use App\Utils\CMS\Exceptions\NotValidSettingRecordException;
use App\Utils\CMS\Setting\SMSDriver\SMSDriverService;
use App\Utils\SMSManager\Drivers\File\Config as FileConfig;
use App\Utils\SMSManager\Drivers\Farapayamak\Config as FaraparamakConfig;
use App\Utils\SMSManager\Drivers\Kavenegar\Config as KavenegarConfig;
use App\Utils\SMSManager\Exceptions\SMSDriverInvalidConfigurationException;
use App\Utils\SMSManager\Models\BaseSMSConfig;

class ConfigProvider {
    static array $CACHED_DATA = [];

    /**
     * @throws SMSDriverInvalidConfigurationException
     */
    public static function getDefaultConfig(string $driver_id): BaseSMSConfig {
        return Factory::driver($driver_id)->getDefaultConfig();
    }

    public static function getConfig(string $driver_id): BaseSMSConfig|FileConfig|FaraparamakConfig|KavenegarConfig {
        if (count(self::$CACHED_DATA) == 0 or !array_key_exists($driver_id, self::$CACHED_DATA)) {
            $sms_driver_setting_record = SMSDriverService::getRecord($driver_id);
            self::$CACHED_DATA[$driver_id] = unserialize($sms_driver_setting_record->getConfigModel());
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
     * @param BaseSMSConfig[] $drivers
     * @throws NotValidSettingRecordException
     * @throws SMSDriverInvalidConfigurationException
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
                SMSDriverService::updateRecord($driver_id, serialize($driver_config));
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
    private static function resetEnabledDrivers(array $other_drivers) {
        foreach ($other_drivers as $other_driver_id => $data) {
            $other_driver_config = self::getConfig($other_driver_id);
            $other_driver_config->is_enabled = false;
            SMSDriverService::updateRecord($other_driver_id, serialize($other_driver_config));
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

    public static function canSendSMSForInvoiceSubmit(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_submit = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_submit = $config->can_send_sms_for_invoice_submit;
        }
        return $can_send_sms_for_invoice_submit;
    }

    public static function canSendSMSForInvoicePaid(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_paid = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_paid = $config->can_send_sms_for_invoice_paid;
        }
        return $can_send_sms_for_invoice_paid;
    }

    public static function canSendSMSForInvoiceCancel(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_cancel = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_cancel = $config->can_send_sms_for_invoice_cancel;
        }
        return $can_send_sms_for_invoice_cancel;
    }

    public static function canSendSMSForInvoiceSending(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_sending = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_sending = $config->can_send_sms_for_invoice_sending;
        }
        return $can_send_sms_for_invoice_sending;
    }

    public static function canSendSMSForInvoiceSent(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_sent = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_sent = $config->can_send_sms_for_invoice_sent;
        }
        return $can_send_sms_for_invoice_sent;
    }

    public static function canSendSMSForInvoiceDelivered(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_delivered = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_delivered = $config->can_send_sms_for_invoice_delivered;
        }
        return $can_send_sms_for_invoice_delivered;
    }

    public static function canSendSMSForInvoiceSurvey(): bool {
        $sms_driver = Provider::getEnabledDriver();
        $can_send_sms_for_invoice_survey = true;
        if (strlen($sms_driver) > 0 and Provider::hasDriver($sms_driver)) {
            $config = self::getConfig($sms_driver);
            $can_send_sms_for_invoice_survey = $config->can_send_sms_for_invoice_survey;
        }
        return $can_send_sms_for_invoice_survey;
    }
}
