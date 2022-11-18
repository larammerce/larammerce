<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 13:14
 */

namespace App\Utils\CMS;


use App\Models\CustomerAddress;
use App\Models\Invoice;
use App\Models\Setting;
use App\Utils\CMS\Cart\Provider as CartProvider;
use App\Utils\CMS\Enums\CMSSettingKey;
use App\Utils\CMS\Setting\ShipmentCost\ShipmentCostService;
use Exception;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class InvoiceService
{
    private static string $CURRENT_INVOICE_KEY = "new_invoice";

    public static function setNew($invoice)
    {
        session()->put(self::$CURRENT_INVOICE_KEY, $invoice);
    }

    public static function hasNew($state): bool
    {
        return session()->has(self::$CURRENT_INVOICE_KEY) and static::getTheNew()?->status >= $state;
    }

    public static function getTheNew(): Invoice
    {
        try {
            return session()->get(self::$CURRENT_INVOICE_KEY) ?? new Invoice();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return new Invoice();
        }
    }

    public static function forgetTheNew()
    {
        session()->forget(self::$CURRENT_INVOICE_KEY);
    }

    public static function createTrackingCode(): string
    {
        $id = Str::random(4);
        return date("ymdHi") . strtoupper($id);
    }

    public static function getStandardShipmentCost($state_id = 0): int
    {
        try {
            $shipment_cost_model = ShipmentCostService::getRecord();
            $custom_states = $shipment_cost_model->getCustomStates();
            if ($state_id !== 0 and array_key_exists($state_id, $custom_states))
                return $custom_states[$state_id]["shipment_cost"];
            return $shipment_cost_model->getShipmentCost();
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function getMinimumPurchaseFreeShipment(): int
    {
        try {
            return ShipmentCostService::getRecord()->getMinimumPurchaseFreeShipment();
        } catch (Exception $e) {
            return 1000000000;
        }
    }

    public static function getMinimumPurchase(): int
    {
        try {
            return intval(Setting::getCMSRecord(CMSSettingKey::MINIMUM_PURCHASE)->value);
        } catch (Exception $e) {
            return 0;
        }
    }

    public static function getShipmentProductCode()
    {
        try {
            return Setting::getCMSRecord(CMSSettingKey::SHIPMENT_PRODUCT_CODE)->value;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function flush($guard = null)
    {
        CartProvider::flush();
    }

    public static function updateAddress(CustomerAddress $customer_address)
    {
        $invoice = static::getTheNew();
        $invoice->updateAddress($customer_address);
        static::setNew($invoice);
    }
}
