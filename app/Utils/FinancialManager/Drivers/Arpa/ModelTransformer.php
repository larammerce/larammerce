<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/15/18
 * Time: 4:45 PM
 */

namespace App\Utils\FinancialManager\Drivers\Arpa;


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
    /**
     * @param stdClass $std_customer
     * @return Customer|boolean
     */
    public static function customerStdToModel(stdClass $std_customer): Customer|bool
    {
        try {
            $customer = new Customer();
            $customer->id = $std_customer->businessID;
            $customer->code = $std_customer->businessCode;
            $customer->nickName = $std_customer->businessName;
            $customer->name = $std_customer->name;
            $customer->family = $std_customer->family;
            $customer->email = $std_customer->email;
            $customer->nationalCode = $std_customer->nationalCode;
            $customer->mobile = $std_customer->mobile;
            $customer->phone = $std_customer->phoneNo;
            $customer->birthday = $std_customer->birthDate;
            $customer->gender = $std_customer->sexuality ? Gender::MALE : Gender::FEMALE;
            $customer->isLegal = !($std_customer->realOrFinancial == 0);
            $customer->economicalCode = $std_customer->finCode;
            $customer->nationalId = $std_customer->idNo;
            $customer->registrationCode = $std_customer->registerNumber;
            $customer->address = $std_customer->address;
            //$customer->stateId = $std_customer->provinceId; //TODO: this item should be returned
            return $customer;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    /**
     * @param Customer $customer
     * @return stdClass|boolean
     */
    public static function customerModelToStd(Customer $customer): stdClass|bool
    {
        try {
            $std_customer = new stdClass();
            $std_customer->BusinessId = $customer->id;
            $std_customer->BusCode = $customer->code;
            $std_customer->BusName = $customer->nickName;
            $std_customer->Name = $customer->name;
            $std_customer->Family = $customer->family;
            $std_customer->Email = $customer->email;
            $std_customer->NationalCode = $customer->nationalCode;
            $std_customer->Mobile = $customer->mobile;
            $std_customer->PhoneNo = $customer->phone;
            $std_customer->BirthDate = $customer->birthday;
            $std_customer->Sexuality = !($customer->gender == Gender::MALE);
            $std_customer->RealOrFinancial = intval($customer->isLegal);
            $std_customer->FinCode = $customer->economicalCode;
            $std_customer->IDNo = $customer->nationalId;
            $std_customer->RegisterNumber = $customer->registrationCode;
            $std_customer->Address = $customer->address;
            $std_customer->ProvinceId = $customer->stateId;
            return $std_customer;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function userToFinCustomer(User $user): Customer
    {
        $customer = new Customer();
        $customer->id = $user->customerUser->fin_relation;
        $customer->name = $user->name;
        $customer->family = $user->family;
        $customer->nickName = $user->name . ' ' . $user->family;
        $customer->email = $user->email;
        $customer->nationalCode = $user->customerUser->national_code;
        $customer->mobile = $user->customerUser->main_phone;
        $customer->gender = $user->gender;
        $customer->isLegal = false;
        return $customer;
    }

    public static function legalUserToFinCustomer(User $user): Customer
    {
        $legalInfo = $user->customerUser->legalInfo;
        $customer = new Customer();
        $customer->id = $legalInfo->fin_relation;
        $customer->name = $legalInfo->company_name;
        $customer->nickName = $legalInfo->company_name;
        $customer->phone = $legalInfo->company_phone;
        $customer->gender = $user->gender;
        $customer->isLegal = true;
        $customer->economicalCode = $legalInfo->economical_code;
        $customer->registrationCode = $legalInfo->registration_code;
        $customer->nationalId = $legalInfo->national_id;
        return $customer;
    }

    public static function userConfigToFinCustomer(Customer $customer, array $config): Customer
    {
        if (isset($config["full_address"]))
            $customer->address = $config["full_address"];
        if (isset($config["state_id"]))
            $customer->stateId = $config["state_id"];
        return $customer;
    }

    public static function productStdToModel(stdClass $stdProduct): Product|bool
    {
        $ratio = ProductService::getPriceRatio();
        try {
            $product = new Product();
            $product->id = $stdProduct->itemID;
            $product->code = $stdProduct->itemCode;
            $product->name = $stdProduct->itemName;
            $product->price = intval(intval($stdProduct->salePrice) * $ratio);
            $product->count = $stdProduct->qty;
            return $product;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function productModelToStd(Product $product): stdClass|bool
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

    public static function invoiceRowModelToStd(InvoiceRow $invoiceRow, $tax_added_to_price = true): stdClass
    {
        $preInvoiceRowStd = new stdClass();

        $preInvoiceRowStd->ItemCode = $invoiceRow->product->code;
        $preInvoiceRowStd->MjQty = $invoiceRow->count;

        $discount_data = $tax_added_to_price ?
            ProductService::reverseCalculateTaxAndToll($invoiceRow->discount_amount) :
            ProductService::calculateTaxAndToll($invoiceRow->discount_amount);

        $preInvoiceRowStd->DiscountAmount = intval($discount_data->price * $invoiceRow->count);
        $preInvoiceRowStd->DiscountPercent = 0;

        $price_data = $tax_added_to_price ?
            ProductService::reverseCalculateTaxAndToll($invoiceRow->product_price) :
            ProductService::calculateTaxAndToll($invoiceRow->product_price);

        $preInvoiceRowStd->Price = $price_data->price;
        $preInvoiceRowStd->TaxAmount = intval($invoiceRow->tax_amount * $invoiceRow->count);
        $preInvoiceRowStd->TollAmount = intval($invoiceRow->toll_amount * $invoiceRow->count);

        return $preInvoiceRowStd;
    }

    public static function invoiceModelToStd(Invoice $invoice, $tax_added_to_price = true): stdClass|bool
    {
        try {
            $preInvoiceStd = new stdClass();
            $customerUser = $invoice->customer;

            $preInvoiceStd->data = new stdClass();
            $preInvoiceStd->data->BusinessId = $customerUser->getFinManRelation($invoice->is_legal);;
            $preInvoiceStd->data->Description = "تحویل گیرنده : " . $invoice->transferee_name . " - " .
                "سفارش دهنده : " . $invoice->customer->user->full_name;

            $preInvoiceStd->items = [];
            foreach ($invoice->rows as $invoiceRow) {
                $preInvoiceRowStd = static::invoiceRowModelToStd($invoiceRow, $tax_added_to_price);
                $preInvoiceStd->items[] = $preInvoiceRowStd;
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
