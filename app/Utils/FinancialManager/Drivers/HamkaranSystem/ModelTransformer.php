<?php namespace App\Utils\FinancialManager\Drivers\HamkaranSystem;


use App\Enums\Customer\Gender;
use App\Models\Invoice;
use App\Models\InvoiceRow;
use App\Models\User;
use App\Services\Invoice\NewInvoiceService;
use App\Utils\CMS\ProductService;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use Carbon\Carbon;
use Exception;
use stdClass;

class ModelTransformer
{
    public static function customerStdToModel(stdClass $std_customer): Customer|bool
    {
        $customer = new Customer();
        $customer->id = $std_customer->CustomerID;
        $customer->isLegal = $std_customer->Type;
        $customer->name = $std_customer->Fname;
        $customer->family = $std_customer->Lname;
        $customer->nickName = $std_customer->CompanyName;
        $customer->nationalCode = $std_customer->NationalID;
        $customer->mobile = $std_customer->Mobile;
        return $customer;
    }

    public static function customerModelToStd(Customer $customer, bool $edited = false): stdClass|bool
    {
        try {
            $std_customer = new stdClass();
            if ($edited)
                $std_customer->ID = $customer->id;
            $std_customer->Alias = null;
            $std_customer->CompanyName = $customer->nickName;
            $std_customer->EconomicCode = $customer->economicalCode;
            $std_customer->FirstName = $customer->name;
            $std_customer->LastName = $customer->family;
            $std_customer->Gender = $customer->gender === Gender::MALE ? 1 : 2;
            $std_customer->Type = $customer->isLegal;
            $std_customer->NationalID = $customer->nationalCode;
            $std_customer->PartyAddresses = $customer->address;
            $std_customer->Phone = $customer->mobile;
            return $std_customer;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function userToFinCustomer(User $user): Customer
    {
        $customer = new Customer();
        $customer->id = 0;
        $customer->name = $user->name;
        $customer->family = $user->family;
        $customer->nickName = null;
        $customer->email = $user->email;
        $customer->nationalCode = $user->customerUser->national_code;
        $customer->mobile = $user->customerUser->main_phone;
        $customer->gender = $user->gender;
        $customer->isLegal = 0;
        return $customer;
    }

    public static function legalUserToFinCustomer(User $user): Customer
    {
        $legalInfo = $user->customerUser->legalInfo;
        $customer = new Customer();
        $customer->id = 0;
        $customer->name = $legalInfo->company_name;
        $customer->nickName = $legalInfo->company_name;
        $customer->phone = $legalInfo->company_phone;
        $customer->gender = $user->gender;
        $customer->isLegal = 1;
        $customer->economicalCode = $legalInfo->economical_code;
        $customer->registrationCode = $legalInfo->registration_code;
        $customer->nationalId = $legalInfo->national_id;
        return $customer;
    }

    public static function productStdToModel(stdClass $stdProduct): Product|bool
    {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        $ratio = $new_invoice_service->getProductPriceRatio();
        try {
            $product = new Product();
            $product->id = $stdProduct->Product ?? -1;
            $product->price = intval(intval($stdProduct->Fee) * $ratio);
            $product->count = $stdProduct->Qty;
            return $product;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function invoiceRowModelToStd(InvoiceRow $invoiceRow): stdClass
    {
        $std_row = new stdClass();
        $std_row->productId = $invoiceRow->hamkaran_product_id;
        $std_row->quantity = $invoiceRow->count;
        $std_row->salesAreaId = 6;
        $std_row->unitId = 1;
        $std_row->type = 1;
        $std_row->fee = $invoiceRow->paymentPrice();
        return $std_row;
    }

    public static function invoiceModelToStd(Invoice $invoice): stdClass|bool
    {
        try {
            $customer = $invoice->customer;
            $address = $invoice->customer_address . " - شماره تماس : " . $invoice->phone_number;
            $now = Carbon::now()->timestamp * 1000;

            $std_invoice = new stdClass();

            $std_invoice->currencyId = 1;
            $std_invoice->customerId = $customer->fin_relation;
            $std_invoice->date = "/Date($now+0330)/";
            $std_invoice->payerType = 1;
            $std_invoice->plantId = 5;
            $std_invoice->salesAreaId = 6;
            $std_invoice->salesOfficeId = 6;
            $std_invoice->salesTypeId = 11;
            $std_invoice->recipientType = 1;
            $std_invoice->description = $address;

            $std_invoice->items = [];
            foreach ($invoice->rows as $invoiceRow) {
                $std_row = static::invoiceRowModelToStd($invoiceRow);
                $std_invoice->items[] = $std_row;
            }

            //TODO: add shipment costs and extras as invoice rows in items.
            //TODO: move hard coded numbers and settings to ecommerce global setting system.

            return $std_invoice;
        }catch (Exception $exception)
        {
            return false;
        }
    }

}
