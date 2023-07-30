<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/15/18
 * Time: 4:45 PM
 */

namespace App\Utils\FinancialManager\Drivers\Arpa;


use App\Enums\Customer\Gender;
use App\Models\Invoice;
use App\Models\InvoiceRow;
use App\Models\User;
use App\Services\Invoice\NewInvoiceService;
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
    public static function customerStdToModel(stdClass $std_customer): Customer|bool {
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
    public static function customerModelToStd(Customer $customer): stdClass|bool {
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

    public static function userToFinCustomer(User $user): Customer {
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

    public static function legalUserToFinCustomer(User $user): Customer {
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

    public static function userConfigToFinCustomer(Customer $customer, array $config): Customer {
        if (isset($config["full_address"]))
            $customer->address = $config["full_address"];
        if (isset($config["state_id"]))
            $customer->stateId = $config["state_id"];
        return $customer;
    }

    public static function productStdToModel(stdClass $stdProduct): Product|bool {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);
        $ratio = $new_invoice_service->getProductPriceRatio();
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

    public static function productModelToStd(Product $product): stdClass|bool {
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

    public static function invoiceRowModelToStd(InvoiceRow $invoice_row, $tax_added_to_price = true): stdClass {
        /** @var NewInvoiceService $new_invoice_service */
        $new_invoice_service = app(NewInvoiceService::class);

        $preInvoiceRowStd = new stdClass();

        $preInvoiceRowStd->ItemCode = $invoice_row->product->code;
        $preInvoiceRowStd->MjQty = $invoice_row->count;

        $discount_data = $tax_added_to_price ?
            $new_invoice_service->reverseCalculateProductTaxAndToll($invoice_row->discount_amount) :
            $new_invoice_service->calculateProductTaxAndToll($invoice_row->discount_amount);

        $preInvoiceRowStd->DiscountAmount = intval($discount_data->price * $invoice_row->count);
        $preInvoiceRowStd->DiscountPercent = 0;

        $price_data = $tax_added_to_price ?
            $new_invoice_service->reverseCalculateProductTaxAndToll($invoice_row->product_price) :
            $new_invoice_service->calculateProductTaxAndToll($invoice_row->product_price);

        $preInvoiceRowStd->Price = $price_data->price;
        $preInvoiceRowStd->TaxAmount = intval($invoice_row->tax_amount * $invoice_row->count);
        $preInvoiceRowStd->TollAmount = intval($invoice_row->toll_amount * $invoice_row->count);

        return $preInvoiceRowStd;
    }

    public static function invoiceModelToStd(Invoice $invoice, $tax_added_to_price = true): stdClass|bool {
        $new_invoice_service = app(NewInvoiceService::class);

        try {
            $pre_invoice_std = new stdClass();
            $customer_user = $invoice->customer;

            $pre_invoice_std->data = new stdClass();
            $pre_invoice_std->data->BusinessId = $customer_user->getFinManRelation($invoice->is_legal);;
            $pre_invoice_std->data->Description = "تحویل گیرنده : " . $invoice->transferee_name . " - " .
                "سفارش دهنده : " . $invoice->customer->user->full_name;

            $pre_invoice_std->items = [];
            foreach ($invoice->rows as $invoice_row) {
                if ($invoice_row->product->is_package) {
                    $product = $invoice_row->product;
                    $items_count = count($product->productPackage->productPackageItems);

                    $latest_price = $product->latest_price;
                    $sum_discount_amount = 0;
                    $sum_tax_amount = 0;
                    $sum_toll_amount = 0;
                    $sum_product_price = 0;
                    $sum_pure_price = 0;
                    foreach ($product->productPackage->productPackageItems as $index => $product_package_item) {
                        $item_product = $product_package_item->product;
                        if (($index + 1) !== $items_count) {
                            $item_latest_price = $item_product->latest_price;
                            $tmp_ratio = (($item_latest_price * $product_package_item->usage_count) / $latest_price);

                            $tmp_discount_amount = (int)($invoice_row->discount_amount * $tmp_ratio);
                            $tmp_tax_amount = (int)($invoice_row->tax_amount * $tmp_ratio);
                            $tmp_toll_amount = (int)($invoice_row->toll_amount * $tmp_ratio);
                            $tmp_product_price = (int)($invoice_row->product_price * $tmp_ratio);
                            $tmp_pure_price = (int)($invoice_row->pure_price * $tmp_ratio);

                            $sum_discount_amount += $tmp_discount_amount;
                            $sum_tax_amount += $tmp_tax_amount;
                            $sum_toll_amount += $tmp_toll_amount;
                            $sum_product_price += $tmp_product_price;
                            $sum_pure_price += $tmp_pure_price;

                        } else {
                            $tmp_discount_amount = $invoice_row->discount_amount - $sum_discount_amount;
                            $tmp_tax_amount = $invoice_row->tax_amount - $sum_tax_amount;
                            $tmp_toll_amount = $invoice_row->toll_amount - $sum_toll_amount;
                            $tmp_product_price = $invoice_row->product_price - $sum_product_price;
                            $tmp_pure_price = $invoice_row->pure_price - $sum_pure_price;
                        }

                        $tmp_invoice_row = new InvoiceRow();
                        $tmp_invoice_row->count = $invoice_row->count * $product_package_item->usage_count;
                        $tmp_invoice_row->product = $item_product;
                        $tmp_invoice_row->discount_amount = (int)($tmp_discount_amount / $product_package_item->usage_count);
                        $tmp_invoice_row->tax_amount = (int)($tmp_tax_amount / $product_package_item->usage_count);
                        $tmp_invoice_row->toll_amount = (int)($tmp_toll_amount / $product_package_item->usage_count);
                        $tmp_invoice_row->product_price = (int)($tmp_product_price / $product_package_item->usage_count);
                        $tmp_invoice_row->pure_price = (int)($tmp_pure_price / $product_package_item->usage_count);

                        $pre_invoice_std->items[] = static::invoiceRowModelToStd($tmp_invoice_row, $tax_added_to_price);
                    }
                } else {
                    $pre_invoice_std->items[] = static::invoiceRowModelToStd($invoice_row, $tax_added_to_price);
                }
            }

            $pre_invoice_std->addsubs = [];
            if ($invoice->has_shipment_cost) {
                $standard_shipment_cost = $invoice->shipment_cost;
                $shipment_cost_exploded = $new_invoice_service->reverseCalculateProductTaxAndToll($standard_shipment_cost);

                $addSub = new stdClass();
                $addSub->AddSubID = $new_invoice_service->getShipmentProductCode();
                $addSub->TASAmount = $shipment_cost_exploded->price;
                $addSub->TASTaxAmount = $shipment_cost_exploded->tax;
                $addSub->TASTollAmount = $shipment_cost_exploded->toll;

                $pre_invoice_std->addsubs[] = $addSub;
            }
            return $pre_invoice_std;
        } catch (Exception $exception) {
            return false;
        }
    }
}
