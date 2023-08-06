<?php

namespace App\Utils\FinancialManager\Drivers\Taraznegar;


use App\Models\Invoice;
use App\Models\User;
use App\Utils\FinancialManager\BaseDriver;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use Exception;
use Illuminate\Support\Facades\Log;

class Driver implements BaseDriver
{
    const DRIVER_ID = "taraznegar";

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
        $result = [];
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/customers', $config)
                ->asJson()
                ->get();
            foreach ($curl_result as $std_customer) {
                $customer = ModelTransformer::customerStdToModel($std_customer);
                if ($customer !== false)
                    $result[] = $customer;
            }
            return $result;
        } catch (Exception $exception) {
            Log::warning("fin_manager.get_all_customers.error :" . $exception->getMessage());
            return $result;
        }
    }

    /**
     * @param string $phone_number
     */
    public function getCustomerByPhone($phone_number): Customer|bool
    {
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/customers/ByPhone', $config)
                ->withData(['phone' => $phone_number])
                ->asJson()
                ->get();
            $std_customer = $curl_result;
            if ($std_customer == null)
                return false;
            return ModelTransformer::customerStdToModel($std_customer);
        } catch (Exception $exception) {
            Log::warning("fin_manager.get_customer_by_phone.error :" . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param string $relation
     */
    public function getCustomerByRelation($relation): Customer|bool
    {
        try {
            $integerId = intval($relation);
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/customers/ByRel', $config)
                ->withData(['rel' => $integerId])
                ->asJson()
                ->get();
            $std_customer = $curl_result;
            if ($std_customer == null)
                return false;
            return ModelTransformer::customerStdToModel($std_customer);
        } catch (Exception $exception) {
            Log::warning("fin_manager.get_customer_by_relation.error : " . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     */
    public function addCustomer($user, $is_legal): bool|string
    {
        $customer = $is_legal ? ModelTransformer::legalUserToFinCustomer($user) : ModelTransformer::userToFinCustomer($user);
        $std_customer = ModelTransformer::customerModelToStd($customer);
        if ($std_customer === false)
            return false;

        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            return ConnectionFactory::create('/customers', $config)
                ->withData($std_customer)->asJson()
                ->post();
        } catch (Exception $exception) {
            Log::warning("fin_manager.add_customer.error : " . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     * @param array $user_config
     * @return boolean
     */
    public function editCustomer($user, $is_legal, $user_config = []): bool
    {
        $customer = $is_legal ? ModelTransformer::legalUserToFinCustomer($user) : ModelTransformer::userToFinCustomer($user);
        $customer = ModelTransformer::userConfigToFinCustomer($customer, $user_config);
        $std_customer = ModelTransformer::customerModelToStd($customer);
        if ($std_customer === false) {
            Log::warning('fin_manager.edit_customer.std_false:' . $user->id . ':' . $is_legal . ':' . json_encode($user));
            return false;
        }
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/customers', $config)
                ->withData($std_customer)->asJson()
                ->put();
            $result = $curl_result;
            if ($result != null) {
                switch ($curl_result) {
                    case -1:
                        Log::warning('fin_manager.edit_customer.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : the Relation not valid');
                        break;
                    case -2:
                        Log::warning('fin_manager.edit_customer.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : the customer with specified relation doesn\'t exist');
                        break;
                    case -3:
                        Log::warning('fin_manager.edit_customer.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : unknown error');
                        break;
                    default :
                        return $result;
                        break;
                }
            }
            Log::warning('fin_manager.edit_customer.result_error:user_id:' . $user->id .
                ':is_legal:' . $is_legal . json_encode($curl_result));
            return false;
        } catch (Exception $exception) {
            Log::warning('fin_manager.edit_customer.error:' . $exception->getMessage());
            return false;
        }
    }

    /**
     * @return Product[]
     */
    public function getAllProducts()
    {
        $result = [];
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/products', $config)
                ->asJson()
                ->get();
            foreach ($curl_result as $stdProduct) {
                $product = ModelTransformer::productStdToModel($stdProduct);
                if ($product !== false)
                    $result[] = $product;
            }
            return $result;
        } catch (Exception $exception) {
            Log::warning('fin_manager.get_all_products.error:' . $exception->getMessage());
            return $result;
        }
    }

    /**
     * @param string $code
     */
    public function getProduct($code): bool|Product
    {
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/products/ByRel', $config)
                ->withData(['rel' => $code])
                ->asJson()
                ->get();
            $stdProduct = $curl_result;
            if ($stdProduct == null)
                return false;
            return ModelTransformer::productStdToModel($stdProduct);
        } catch (Exception $exception) {
            Log::warning('fin_manager.get_product.error:' . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param string $code
     */
    public function getProductCount($code): bool|int
    {
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/serv/api/GetStock', $config)
                ->withData(['ItemCode' => $code])
                ->asJson()
                ->get();
            $count = intval($curl_result->data[0]->stock);
        } catch (Exception $exception) {
            Log::warning('fin_manager.get_product_count.error:' . $exception->getMessage());
            return false;
        }
        return $count;
    }

    /**
     * @param Invoice $invoice
     */
    public function addPreInvoice($invoice): bool|string
    {
        $customerUser = $invoice->customer;
        $updateFinManAddressResult = $customerUser->updateFinManAddress($invoice->is_legal,
            $invoice->customer_address . " - شماره تماس : " . $invoice->phone_number,
            $invoice->state_id);

        if ($updateFinManAddressResult === false) {
            Log::warning('fin_manager.add_pre_invoice: ' . $invoice->id . ':address_not_updated');
            return false;
        }

        $stdPreInvoice = ModelTransformer::invoiceModelToStd($invoice);
        try {
            if ($stdPreInvoice != false) {
                $config = ConfigProvider::getConfig(self::DRIVER_ID);
                $curl_result = ConnectionFactory::create('/invoice', $config)
                    ->withData($stdPreInvoice)->asJson()
                    ->post();
                switch ($curl_result) {
                    case 0:
                        Log::warning('fin_manager.add_pre_invoice.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : something went wrong');
                        break;
                    default :
                        return $curl_result;
                }
            }
            Log::warning('fin_manager.add_pre_invoice.error:' . $invoice->id . ':' . json_encode($invoice));
            return false;
        } catch (Exception $exception) {
            Log::warning("fin_manager.add_pre_invoice.error :" . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param string $fin_relation
     */
    public function deletePreInvoice($fin_relation): bool
    {
        if ($fin_relation == null)
            return false;
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/invoice/Delete', $config)
                ->withData([
                    "relation" => $fin_relation
                ])->asJson()
                ->get();
            if (isset($curl_result)) {
                switch ($curl_result) {
                    case -1:
                        Log::warning('fin_manager.delete_pre_invoice.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : There is no factor with this relation id');
                        break;
                    case -2:
                        Log::warning('fin_manager.delete_pre_invoice.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : This factor is not PreInvoice and cannot be deleted');
                        break;
                    case -3:
                        Log::warning('fin_manager.delete_pre_invoice.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : This factor is PreInvoice but already convert to invoice then cannot be deleted');
                        break;
                    case -4:
                        Log::warning('fin_manager.delete_pre_invoice.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : Unknown error');
                        break;
                    default :
                        return true;
                        break;
                }
            }
            Log::warning("fin_manager.delete_pre_invoice : " . json_encode($curl_result));
            return false;
        } catch (Exception $exception) {
            Log::warning("fin_manager.delete_pre_invoice.error :" . $exception->getMessage());
            return false;
        }

    }

    /**
     * @param string $fin_relation
     */
    public function submitWarehousePermission($fin_relation): bool|string
    {
        if ($fin_relation == null)
            return false;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/invoice/convert', $config)
            ->withData([
                "relation" => $fin_relation,
            ])
            ->asJson()
            ->get();
        try {
            $result = $curl_result;
            if ($result != null) {
                switch ($curl_result) {
                    case -1:
                        Log::warning('fin_manager.submit_warehouse_permission.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : PreInvoice with specified relation doesn\'t exist');
                        break;
                    case -3:
                        Log::warning('fin_manager.submit_warehouse_permission.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : This PreInvoice Already converted to Complete Invoice');
                        break;
                    case -4:
                        Log::warning('fin_manager.submit_warehouse_permission.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data :Unknown error');
                        break;
                    default :
                        return $result;
                }
            }
            Log::warning("fin_manager.submit_warehouse_permission.error : curlResult null data");
            return false;
        } catch (Exception $exception) {
            Log::warning("fin_manager.submit_warehouse_permission.error :" . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param string $fin_relation
     */
    public function checkExitTab($fin_relation): bool
    {
        if ($fin_relation == null)
            return false;
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create('/invoice/havale', $config)
                ->withData([
                    "relation" => $fin_relation,
                ])->asJson()
                ->get();
            $result = $curl_result;
            if ($result != null) {
                switch ($curl_result) {
                    case -1:
                        Log::warning('fin_manager.check_exit_tab.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : Invoice with specified relation doesn\'t exist');
                        break;
                    case 0:
                        Log::warning('fin_manager.check_exit_tab.data_parse_exception: ' .
                            json_encode($curl_result) . ' passed_data : Havale doesn\'t submit yet');
                        break;
                    default :
                        return $result;
                }
            }
            Log::warning("fin_manager.check_exit_tab.error : curlResult null data");
            return false;
        } catch (Exception $exception) {
            Log::warning("fin_manager.check_exit_tab.error :" . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param integer $standard_price
     */
    public function convertPrice($standard_price): int
    {
        return $standard_price;
    }
}
