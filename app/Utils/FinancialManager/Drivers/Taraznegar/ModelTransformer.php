<?php

namespace App\Utils\FinancialManager\Drivers\Taraznegar;

use App\Models\Enums\Gender;
use App\Models\Invoice;
use App\Models\InvoiceRow;
use App\Models\User;
use App\Utils\CMS\InvoiceService;
use App\Utils\CMS\ProductService;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;
use stdClass;

class ModelTransformer
{
    public static function customerStdToModel(stdClass $std_customer): Customer|bool
    {

        try {
            $customer = new Customer();
            $customer->id = $std_customer->relationValue;
            $customer->name = $std_customer->name;
            $customer->family = $std_customer->family;
            $customer->email = $std_customer->email;
            $customer->nationalCode = $std_customer->nationalCode;
            $customer->mobile = $std_customer->mainPhone;
            $customer->gender = $std_customer->gender ? Gender::MALE : Gender::FEMALE;
            $customer->isLegal = !($std_customer->isLegalPerson == 0);
            $customer->economicalCode = $std_customer->economicalCode;
            $customer->nationalId = $std_customer->nationalId;
            $customer->registrationCode = $std_customer->registrationCode;
            $customer->companyName = $std_customer->companyName;
            return $customer;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    public static function customerModelToStd(Customer $customer): bool|stdClass
    {
        try {
            $std_customer = new stdClass();
            $std_customer->isLegalPerson = $customer->isLegal ? 1 : 0;
            $std_customer->name = $customer->name;
            $std_customer->family = $customer->family;
            $std_customer->mainPhone = $customer->phone;
            $std_customer->nationalCode = $customer->nationalCode;
            $std_customer->email = $customer->email;
            $std_customer->gender = !($customer->gender == Gender::MALE);
            $std_customer->economicalCode = $customer->economicalCode;
            $std_customer->registrationCode = $customer->registrationCode;
            $std_customer->nationalId = $customer->nationalId;
            $std_customer->companyName = $customer->companyName;
            $std_customer->relationValue = $customer->id;
            return $std_customer;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function userToFinCustomer(User $user): Customer
    {
        $customer = new Customer();
        $customer->id = $user->customerUser->fin_relation;
        $customer->isLegal = false;
        $customer->name = $user->name;
        $customer->family = $user->family;
        $customer->phone = $user->customerUser->main_phone;
        $customer->email = $user->email;
        $customer->gender = $user->gender;
        $customer->economicalCode = null;
        $customer->nationalCode = $user->customerUser->national_code;
        $customer->registrationCode = null;
        $customer->nationalId = null;
        $customer->companyName = null;
        return $customer;
    }

    public static function legalUserToFinCustomer(User $user): Customer
    {
        $legalInfo = $user->customerUser->legalInfo;
        $customer = new Customer();
        $customer->id = $user->customerUser->fin_relation;
        $customer->isLegal = true;
        $customer->name = $user->name;
        $customer->family = $user->family;
        $customer->phone = $legalInfo->company_phone;
        $customer->email = $user->email;
        $customer->gender = $user->gender;
        $customer->economicalCode = $legalInfo->economical_code;
        $customer->registrationCode = $legalInfo->registration_code;
        $customer->nationalCode = $user->customerUser->national_code;
        $customer->nationalId = $legalInfo->national_id;
        $customer->companyName = $legalInfo->company_name;
        return $customer;
    }

    public static function userConfigToFinCustomer(Customer $customer, array $user_config): Customer
    {
        if (isset($user_config["full_address"]))
            $customer->address = $user_config["full_address"];
        if (isset($user_config["state_id"]))
            $customer->stateId = $user_config["state_id"];
        return $customer;
    }

    public static function productStdToModel(stdClass $stdProduct): bool|Product
    {
        $ratio = ProductService::getPriceRatio();
        try {
            $product = new Product();
            $product->id = $stdProduct->relation;
            $product->code = $stdProduct->relation;
            $product->name = $stdProduct->title;
            $product->price = intval(intval($stdProduct->price) * $ratio);
            $product->count = $stdProduct->quantity;
            return $product;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function productModelToStd(Product $product): bool|stdClass
    {
        try {
            $stdProduct = new stdClass();
            $stdProduct->itemID = $product->id;
            $stdProduct->itemCode = $product->code;
            $stdProduct->itemName = $product->name;
            $stdProduct->salePrice = intval($product->price);
            $stdProduct->qty = $product->count;
            return $stdProduct;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function invoiceRowModelToStd(InvoiceRow $invoiceRow): stdClass
    {
        $preInvoiceRowStd = new stdClass();
        $preInvoiceRowStd->ProductRelation = $invoiceRow->product->code;
        $preInvoiceRowStd->Count = $invoiceRow->count;
        $preInvoiceRowStd->DiscountAmount = intval($invoiceRow->discount_amount * $invoiceRow->count);
        $preInvoiceRowStd->DiscountPercent = 0;
        $preInvoiceRowStd->PurePrice = intval($invoiceRow->product_price);
        $preInvoiceRowStd->TaxAmount = intval($invoiceRow->tax_amount * $invoiceRow->count);
        $preInvoiceRowStd->TollAmount = intval($invoiceRow->toll_amount * $invoiceRow->count);
        return $preInvoiceRowStd;
    }

    public static function invoiceModelToStd(Invoice $invoice): bool|stdClass
    {
        try {
            $customerUser = $invoice->customer;
            $preInvoiceStd = new stdClass();
            $preInvoiceStd->CustomerRelation = $customerUser->getFinManRelation($invoice->is_legal);
            $preInvoiceStd->Description = "تحویل گیرنده : " . $invoice->transferee_name . " - " .
                "سفارش دهنده : " . $invoice->customer->user->full_name . "\n" .
                $invoice->getCMIComment();

            $preInvoiceStd->Items = [];
            foreach ($invoice->rows as $invoiceRow) {
                $preInvoiceRowStd = static::invoiceRowModelToStd($invoiceRow);
                $preInvoiceStd->Items[] = $preInvoiceRowStd;
            }

            $preInvoiceStd->addsubs = [];
            if ($invoice->has_shipment_cost) {

                $standardShipmentCost = $invoice->shipment_cost;
                $shipmentCostExploded = ProductService::reverseCalculateTaxAndToll($standardShipmentCost);

                $addSub = new stdClass();
                $addSub->AddSubID = InvoiceService::getShipmentProductCode();
                $addSub->TASAmount = $shipmentCostExploded->price;
                $addSub->TASTaxAmount = $shipmentCostExploded->tax;
                $addSub->TASTollAmount = $shipmentCostExploded->toll;

                $preInvoiceStd->addsubs[] = $addSub;
            }

            return $preInvoiceStd;
        }catch (Exception $exception){
            return false;
        }
    }
}
