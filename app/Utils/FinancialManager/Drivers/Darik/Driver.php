<?php

namespace App\Utils\FinancialManager\Drivers\Darik;

use App\Models\User;
use App\Utils\FinancialManager\BaseDriver;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\FinancialManager\Models\BaseFinancialConfig;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use Illuminate\Support\Str;
use stdClass;

class Driver implements BaseDriver {
    const DRIVER_ID = "darik";

    public function getId(): string {
        return self::DRIVER_ID;
    }

    public function getDefaultConfig(): BaseFinancialConfig {
        return new Config();
    }

    /**
     * @return array|Customer[]
     */
    public function getAllCustomers(): array {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create($config)
            ->withData([
                "query" => Queries::ALL_CUSTOMERS
            ])
            ->asJson()
            ->post();

        if ($curl_result?->data?->customer_getCustomers?->status !== "SUCCESS") {
            return [];
        }

        return array_map(function (stdClass $std_customer) {
            return ModelTransformer::customerStdToModel($std_customer);
        }, $curl_result->data->customer_getCustomers->result?->items ?? []);
    }

    /**
     * @param string $phone_number
     * @return Customer|boolean
     */
    public function getCustomerByPhone($phone_number): Customer|bool {
        return false;
    }

    /**
     * @param $relation
     * @return Customer|boolean
     */
    public function getCustomerByRelation($relation): Customer|bool {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create($config)
            ->withData([
                "query" => Str::swap([
                    ":relationId" => $relation
                ], Queries::CUSTOMER_BY_RELATION)
            ])
            ->asJson()
            ->post();

        if ($curl_result?->data?->customer_getCustomer?->status !== "SUCCESS") {
            return false;
        }

        return ModelTransformer::customerStdToModel($curl_result->data->customer_getCustomer->result);
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     * @return string|boolean
     */
    public function addCustomer($user, $is_legal): bool|string {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create($config)
            ->withData([
                "query" => Str::swap(
                    ModelTransformer::getCustomerDataFromUserModel($user, $is_legal),
                    Mutations::CREATE_CUSTOMER
                )
            ])
            ->asJson()
            ->post();


        if ($curl_result?->data?->customer_createCustomer?->status !== "SUCCESS")
            return false;

        return $curl_result->data->customer_createCustomer->result?->relationId;
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     * @param array $user_config
     * @return boolean
     */
    public function editCustomer($user, $is_legal, $user_config = []): bool {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create($config)
            ->withData([
                "query" => Str::swap(
                    ModelTransformer::getCustomerDataFromUserModel($user, $is_legal),
                    Mutations::EDIT_CUSTOMER
                )
            ])
            ->asJson()
            ->post();

        if ($curl_result?->data?->customer_editCustomer?->status !== "SUCCESS")
            return false;

        return true;
    }

    /**
     * @return Product[]
     */
    public function getAllProducts(): array {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create($config)
            ->withData([
                "query" => Queries::ALL_PRODUCTS
            ])
            ->asJson()
            ->post();

        if ($curl_result?->data?->proudct_getProducts?->status !== "SUCCESS") {
            return [];
        }

        return array_map(function (stdClass $product_std) {
            return ModelTransformer::productStdToModel($product_std);
        }, $curl_result->data->proudct_getProducts->result?->items ?? []);
    }

    /**
     * @param string $code
     * @return Product|boolean
     */
    public function getProduct($code): bool|Product {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create($config)
            ->withData([
                "query" => Str::swap([
                    ":relationId" => $code
                ], Queries::PRODUCT_BY_RELATION)
            ])
            ->asJson()
            ->post();

        if ($curl_result?->data?->proudct_getProduct?->status !== "SUCCESS") {
            return false;
        }

        return ModelTransformer::productStdToModel($curl_result->data->proudct_getProduct->result);
    }

    /**
     * @param string $code
     * @return integer|boolean
     */
    public function getProductCount($code): bool|int {
        return $this->getProduct($code)->count;
    }

    public function addPreInvoice($invoice) {
        // TODO: Implement addPreInvoice() method.
    }

    public function deletePreInvoice($fin_relation) {
        // TODO: Implement deletePreInvoice() method.
    }

    public function submitWarehousePermission($fin_relation) {
        // TODO: Implement submitWarehousePermission() method.
    }

    public function checkExitTab($warehouse_permission_data) {
        // TODO: Implement checkExitTab() method.
    }

    public function convertPrice($standard_price): int {
        return $standard_price;
    }
}