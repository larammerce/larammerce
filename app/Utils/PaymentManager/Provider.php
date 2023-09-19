<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/26/18
 * Time: 2:04 PM
 */

namespace App\Utils\PaymentManager;

use App\Models\Invoice;
use App\Models\Payment;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidDriverException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidParametersException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class Provider {
    const PAYMENT_REDIRECTION_URL = "/payment-redirection";

    public static function getPaymentRedirectionUrl(): string {
        return static::PAYMENT_REDIRECTION_URL;
    }

    public static function publishRoutes() {
        Route::group(["namespace" => "App\\Utils\\PaymentManager", "middleware" => "web"], function ($router) {
            $router->get(static::PAYMENT_REDIRECTION_URL, "PaymentController@paymentRedirection");
            $router->any(AbstractDriver::CALLBACK_URL_PREFIX . "/{driver_name}", "PaymentController@bankCallback");
        });
    }

    public static function getAllDrivers(): array {
        return array_keys(Kernel::$drivers);
    }

    /**
     * @param bool $needsData
     * @param bool $needsString
     * @return string[]|AbstractDriver[]|string
     * @throws PaymentInvalidDriverException
     */
    public static function getEnabledDrivers(bool $needsData = false, bool $needsString = false) {
        $all_drivers_ids = self::getAllDrivers();
        $enabled_driver_names = [];

        foreach ($all_drivers_ids as $driver_id) {
            try {
                $driver_config = ConfigProvider::getConfig($driver_id);
                if ($driver_config->is_enabled)
                    $enabled_driver_names[] = $driver_id;
            } catch (PaymentDriverNotConfiguredException $e) {
                Log::error("Payment driver not configured.
                 getEnabledDrivers: getConfigData: getDriver:{$e->getMessage()}");
            }
        }

        if ($needsString)
            if (count($enabled_driver_names) > 0)
                return join(",", $enabled_driver_names);
            else
                return '';

        if (!$needsData)
            return $enabled_driver_names;

        $result = [];
        foreach ($enabled_driver_names as $driver_name) {
            if (strlen($driver_name) > 0) {
                $result[] = Factory::driver($driver_name);
            }
        }
        return $result;
    }

    public static function getDefaultDriver(): string {
        $all_drivers_ids = self::getAllDrivers();
        foreach ($all_drivers_ids as $driver_id) {
            try {
                $driver_config = ConfigProvider::getConfig($driver_id);
                if ($driver_config->is_default)
                    return $driver_id;
            } catch (PaymentDriverNotConfiguredException $e) {
                Log::error("Payment driver not configured.
                 getDefaultDriver:getConfigData:getDriver:{$e->getMessage()}");
            }
        }
        return '';
    }

    public static function isDefaultDriver(string $driver_name): bool {
        return $driver_name === static::getDefaultDriver();
    }

    public static function hasDriver(string $driver_name): bool {
        return array_key_exists($driver_name, Kernel::$drivers);
    }

    /**
     * @throws PaymentInvalidDriverException
     * @throws PaymentConnectionException
     * @throws PaymentInvalidParametersException
     */
    public static function initiatePayment(Invoice $invoice, string $driver_name = null) {
        if ($driver_name == null)
            $driver_name = static::getDefaultDriver();

        if ($driver_name == null or $driver_name == 'NON' or strlen($driver_name) == 0) {
            throw new PaymentInvalidDriverException("No valid driver is selected to initiate payment for " .
                "invoice '{$invoice->id}'.");
        }

        $payment = $invoice->payments()->where("payment_data", null)->first();
        if ($payment == null) {
            $payment = Payment::create([
                "invoice_id" => $invoice->id,
                "amount" => intval($invoice->sum),
                "driver" => $driver_name
            ]);
        } else {
            $payment->driver = $driver_name;
        }

        try {
            $payment->payment_data = Factory::driver($driver_name)->initiatePayment($payment->amount, $payment->id, [
                "phone_number" => $invoice->customer->main_phone,
                "email" => $invoice->customer->user->email
            ]);
            $payment->save();
        } catch (PaymentConnectionException $e) {
            $payment->delete();
            throw new PaymentConnectionException($e->getMessage());
        }
    }
}
