<?php
/**
 * Created by PhpStorm.
 * User: mamareza
 * Date: 9/28/2017 AD
 * Time: 13:14
 */

namespace App\Services\Invoice;


use App\Enums\Setting\CMSSettingKey;
use App\Helpers\CMSSettingHelper;
use App\Models\Invoice;
use App\Models\Product;
use App\Utils\CMS\Cart\Provider as CartProvider;
use App\Utils\CMS\Setting\ShipmentCost\ShipmentCostService;
use App\Utils\FinancialManager\ConfigProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use stdClass;

class NewInvoiceService {
    private Request $request;
    private CMSSettingHelper $setting_service;

    public function __construct(Request $request, CMSSettingHelper $setting_service) {
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

    public function getProductTollPercentage(Product $product = null): float {
        if (ConfigProvider::shouldUsePerProductTaxConfig() and !is_null($product) and
            !$product->use_default_tax_params and !is_null($product->toll_percentage)) {
            return $product->toll_percentage;
        } else {
            return ConfigProvider::getDefaultTollPercentage();
        }
    }

    public function getProductTaxPercentage(Product $product = null): float {
        if (ConfigProvider::shouldUsePerProductTaxConfig() and !is_null($product) and
            !$product->use_default_tax_params and !is_null($product->tax_percentage)) {
            return $product->tax_percentage;
        } else {
            return ConfigProvider::getDefaultTaxPercentage();
        }
    }

    public function getProductAllExtrasPercentage(Product $product = null): float {
        return $this->getProductTollPercentage($product) + $this->getProductTaxPercentage($product);
    }

    public function isTaxAddedToPrice(Product $product = null): bool {
        if (ConfigProvider::shouldUsePerProductTaxConfig() and !is_null($product)) {
            return $product->is_tax_included;
        } else {
            return ConfigProvider::isTaxAddedToPriceByDefault();
        }
    }

    public function getProductTollAmount(?int $product_pure_price, int $count = 1, Product $product = null): int {
        if (is_null($product_pure_price)) {
            $product_pure_price = 0;
        }
        return intval(($product_pure_price * $count) * $this->getProductTollPercentage($product) / 100);
    }

    public function getProductTaxAmount(?int $product_pure_price, int $count = 1, Product $product = null): int {
        if (is_null($product_pure_price)) {
            $product_pure_price = 0;
        }
        return intval(($product_pure_price * $count) * $this->getProductTaxPercentage($product) / 100);
    }

    public function getProductPurePrice(?int $product_total_price, Product $product = null): int {
        if ($product_total_price === 0 or is_null($product_total_price))
            return $product_total_price;

        $tax_and_toll_percentage = $this->getProductTaxPercentage($product) + $this->getProductTollPercentage($product);
        return intval($product_total_price * 100 / (100 + $tax_and_toll_percentage)) + 1;
    }

    public function reverseCalculateProductTaxAndToll(?int $product_total_price, int $count = 1, Product $product = null): stdClass {
        if (is_null($product_total_price)) {
            $product_total_price = 0;
        }

        $result = new stdClass();
        $result->price = $this->getProductPurePrice($product_total_price, product: $product);
        $result->tax = $this->getProductTaxAmount($result->price, $count, product: $product);
        $result->toll = intval($product_total_price - ($result->price + $result->tax));

        if ($result->toll < 0) {
            $result->price = $result->price + $result->toll;
            $result->toll = 0;
        }

        if ($result->tax < 0) {
            $result->price = $result->price + $result->tax;
            $result->tax = 0;
        }

        return $result;
    }

    public function calculateProductTaxAndToll(?int $pure_price, int $count = 1, Product $product = null): stdClass {
        if (is_null($pure_price)) {
            $pure_price = 0;
        }
        $result = new stdClass();
        $result->price = $pure_price;
        $result->tax = $this->getProductTaxAmount($result->price, $count, product: $product);
        $result->toll = $this->getProductTollAmount($result->price, $count, product: $product);

        return $result;
    }

    public function getProductPriceRatio(): float {
        return doubleval(env('SITE_PRICE_RATIO', '1.0'));
    }
}
