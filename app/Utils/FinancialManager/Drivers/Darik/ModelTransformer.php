<?php

namespace App\Utils\FinancialManager\Drivers\Darik;

use App\Models\Enums\Gender;
use App\Models\Invoice;
use App\Models\InvoiceRow;
use App\Models\User;
use App\Utils\CMS\ProductService;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use stdClass;

class ModelTransformer {
    /**
     * @param stdClass $std_customer
     * @return Customer|boolean
     */
    public static function customerStdToModel(stdClass $std_customer): Customer|bool {
        $customer = new Customer();
        $customer->id = $std_customer->relationId;
        $customer->name = $std_customer->name;
        $customer->nickName = "";
        $customer->family = "";
        $customer->address = "";
        $customer->code = $std_customer->relationId;
        $customer->email = "";
        $customer->birthday = "";
        $customer->companyName = $std_customer->companyName;
        $customer->economicalCode = $std_customer->ecoCode;
        $customer->gender = ($std_customer->gender === "MALE") ? Gender::MALE : Gender::FEMALE;
        $customer->isLegal = ($std_customer->personType !== "HAGHIGHI");
        $customer->mobile = $std_customer->mobile;
        $customer->phone = $std_customer->phone;
        $customer->nationalCode = $std_customer->nationalCode;
        $customer->registrationCode = $std_customer->registrationCode;
        $customer->stateId = "";

        return $customer;
    }

    public static function getCustomerDataFromUserModel(User $user, bool $is_legal): array {
        if ($is_legal)
            return [
                ":companyName" => $user->customerUser->legalInfo->company_name,
                ":ecoCode" => $user->customerUser->legalInfo->economical_code,
                ":email" => $user->email,
                ":gender" => (($user->gender === Gender::MALE) ? "MALE" : "FEMALE"),
                ":mobile" => $user->customerUser->main_phone,
                ":name" => $user->full_name,
                ":nationalCode" => $user->customerUser->national_code,
                ":nationalId" => $user->customerUser->legalInfo->national_id,
                ":personType" => "HOGHUGHI",
                ":phone" => $user->customerUser->legalInfo->company_phone,
                ":registrationCode" => $user->customerUser->legalInfo->registration_code,
                ":relationId" => intval($user->customerUser->legalInfo->fin_relation)
            ];
        return [
            ":companyName" => "",
            ":ecoCode" => "",
            ":email" => $user->email,
            ":gender" => (($user->gender === Gender::MALE) ? "MALE" : "FEMALE"),
            ":mobile" => $user->customerUser->main_phone,
            ":name" => $user->full_name,
            ":nationalCode" => $user->customerUser->national_code,
            ":nationalId" => "",
            ":personType" => "HAGHIGHI",
            ":phone" => $user->customerUser->main_phone,
            ":registrationCode" => "",
            ":relationId" => intval($user->customerUser->fin_relation)
        ];
    }

    public static function userConfigToFinCustomer(Customer $customer, array $config): Customer {

    }

    public static function productStdToModel(stdClass $stdProduct): Product|bool {
        $ratio = ProductService::getPriceRatio();
        try {
            $product = new Product();
            $product->id = $stdProduct->relationId;
            $product->code = $stdProduct->relationId;
            $product->name = $stdProduct->name;
            $product->price = intval(intval($stdProduct->price) * $ratio);
            $product->count = $stdProduct->quantity;
            return $product;
        } catch (Exception $exception) {
            return false;
        }
    }

    public static function invoiceRowModelToStd(InvoiceRow $invoiceRow, $tax_added_to_price = true): stdClass {

    }

    public static function invoiceModelToStd(Invoice $invoice, $tax_added_to_price = true): stdClass|bool {

    }
}