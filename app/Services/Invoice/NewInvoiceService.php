<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 13:14
 */

namespace App\Services\Invoice;


use App\Enums\Setting\CMSSettingKey;
use App\Models\CustomerAddress;
use App\Models\Invoice;
use App\Services\Setting\SettingService;
use App\Utils\CMS\AdminRequestService;
use App\Utils\CMS\Cart\Provider as CartProvider;
use App\Utils\CMS\Setting\ShipmentCost\ShipmentCostService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class NewInvoiceService
{
    private Request $request;
    private SettingService $setting_service;

    public function __construct(Request $request, SettingService $setting_service) {
        $this->request = $request;
        $this->setting_service = $setting_service;
    }

    private const CURRENT_INVOICE_KEY = "new_invoice";

    public function setTheNew(Invoice $invoice): void {
        $this->request->session()->put(self::CURRENT_INVOICE_KEY, $invoice);
    }

    public function hasNew($state): bool {
        return $this->request->session()->has(self::CURRENT_INVOICE_KEY) and static::getTheNew()?->status >= $state;
    }

    public function getTheNew(): Invoice {
        try {
            return $this->request->session()->get(self::CURRENT_INVOICE_KEY) ?? new Invoice();
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            return new Invoice();
        }
    }

    public function forgetTheNew(): void {
        $this->request->session()->forget(self::CURRENT_INVOICE_KEY);
    }

    public function createTrackingCode(): string {
        $id = Str::random(4);
        return date("ymdHi") . strtoupper($id);
    }

    public function getStandardShipmentCost($state_id = 0): int {
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

    public function getMinimumPurchaseFreeShipment(): int {
        try {
            return ShipmentCostService::getRecord()->getMinimumPurchaseFreeShipment();
        } catch (Exception $e) {
            return 1000000000;
        }
    }

    public function getMinimumPurchase(): int {
        return $this->setting_service->getCMSSettingAsInt(CMSSettingKey::MINIMUM_PURCHASE);
    }

    public function getShipmentProductCode(): string {
        return $this->setting_service->getCMSSettingAsString(CMSSettingKey::SHIPMENT_PRODUCT_CODE);
    }

    public function flush($guard = null): void {
        CartProvider::flush();
    }

    public function updateAddress(CustomerAddress $customer_address): void {
        if (AdminRequestService::isInAdminArea())
            return;
        $invoice = $this->getTheNew();
        $invoice->updateAddress($customer_address);
        $this->setTheNew($invoice);
    }
}
