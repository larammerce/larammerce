<?php

namespace App\Utils\FinancialManager\Drivers\Local;


use App\Models\Invoice;
use App\Models\User;
use App\Utils\FinancialManager\BaseDriver;
use App\Utils\FinancialManager\Exceptions\InvalidFinRelationException;
use App\Utils\FinancialManager\Exceptions\StockCountException;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use Exception;
use Throwable;

class Driver implements BaseDriver
{
    const DRIVER_ID = "local";

    public function getId(): string
    {
        return self::DRIVER_ID;
    }

    public function getDefaultConfig(): Config
    {
        return new Config();
    }

    /**
     * @return Customer[]
     */
    public function getAllCustomers()
    {
        return [];
    }

    /**
     * @param string $phone_number
     */
    public function getCustomerByPhone($phone_number): Customer|bool
    {
        return true;
    }

    /**
     * @param string $relation
     */
    public function getCustomerByRelation($relation): Customer|bool
    {
        return true;
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     */
    public function addCustomer($user, $is_legal): string|bool
    {
        return "local_customer";
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     * @param array $config
     */
    public function editCustomer($user, $is_legal, $config = []): bool
    {
        return true;
    }

    /**
     * @return Product[]
     */
    public function getAllProducts()
    {
        return [];
    }

    /**
     * @param string $code
     */
    public function getProduct($code): bool|Product
    {
        return true;
    }

    /**
     * @param string $code
     */
    public function getProductCount($code): bool|int
    {
        return true;
    }

    /**
     * @param Invoice $invoice
     */
    public function addPreInvoice($invoice): bool|string
    {
        try {
            foreach ($invoice->rows as $invoice_row) {
                $product = $invoice_row->product;
                if ($invoice_row->count > $product->maximum_allowed_purchase_count) {
                    throw new StockCountException("The product `{$product->id}` is not sufficient for selling.",
                        $product->id);
                }
                if($product->is_package)
                {
                    $product_package = $product->productPackage;
                    foreach ($product_package->products as $used_product)
                    {
                        //TODO: Use App\Models\ProductPackageItem to get used_count
                        $used_product->count -= $invoice_row->count *
                            $product_package->getItemUsageCount($used_product->id);
                        $used_product->save();
                    }
                }else {
                    $product->count -= $invoice_row->count;
                    $product->save();
                }
            }
        } catch (Exception | Throwable $e) {
            return false;
        }
        return "{$invoice->id}";
    }

    /**
     * @param string $fin_relation
     * @throws InvalidFinRelationException
     */
    public function deletePreInvoice($fin_relation): bool
    {
        $invoice = Invoice::where("fin_relation", "=", $fin_relation)->first();
        if ($invoice == null)
            throw new InvalidFinRelationException("The fin_relation `{$fin_relation}` is not valid.");
        try {
            foreach ($invoice->rows as $invoice_row) {
                $product = $invoice_row->product;
                if($product->is_package)
                {
                    $product_package = $product->productPackage;
                    foreach ($product_package->products as $used_product)
                    {
                        $used_product->count += $invoice_row->count *
                            $product_package->getItemUsageCount($used_product->id);
                        $used_product->save();
                    }
                }else {
                    $product->count += $invoice_row->count;
                    $product->save();
                }
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $fin_relation
     */
    public function submitWarehousePermission($fin_relation): bool|string
    {
        return "local_invoice";
    }

    /**
     * @param string $warehouse_permission_data
     * @return boolean
     */
    public function checkExitTab($warehouse_permission_data): bool
    {
        return true;
    }

    /**
     * @param integer $standard_price
     */
    public function convertPrice($standard_price): int
    {
        return $standard_price;
    }
}
