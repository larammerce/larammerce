<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/12/18
 * Time: 12:34 PM
 */

namespace App\Utils\FinancialManager\Drivers\Arpa;


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
    const DRIVER_ID = 'arpa';

    public function getId(): string {
        return self::DRIVER_ID;
    }

    public function getDefaultConfig(): Config {
        return new Config();
    }

    /**
     * @return Customer[]
     */
    public function getAllCustomers() {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/GetBusiness', $config)
            ->asJson()
            ->get();
        $result = [];
        foreach ($curl_result->data as $std_customer) {
            $customer = ModelTransformer::customerStdToModel($std_customer);
            if ($customer !== false)
                $result[] = $customer;
        }
        return $result;
    }

    /**
     * @param string $phone_number
     */
    public function getCustomerByPhone($phone_number): Customer|bool {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/GetBusiness', $config)
            ->withData(['MobileNo' => $phone_number])
            ->asJson()
            ->get();

        try {
            $std_customer = $curl_result->data[0];
        } catch (Exception $e) {
            return false;
        }
        if ($std_customer == null)
            return false;
        return ModelTransformer::customerStdToModel($std_customer);
    }

    /**
     * @param string $relation
     */
    public function getCustomerByRelation($relation): Customer|bool {
        $integerId = intval($relation);
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/GetBusiness', $config)
            ->withData(['BusinessId' => $integerId])
            ->asJson()
            ->get();

        try {
            $std_customer = $curl_result->data[0];
        } catch (Exception $e) {
            Log::warning("fin_manager.get_customer_by_relation : " . $e->getMessage());
            return false;
        }
        if ($std_customer == null)
            return false;
        return ModelTransformer::customerStdToModel($std_customer);
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     */
    public function addCustomer($user, $is_legal): string|bool {
        $customer = $is_legal ? ModelTransformer::legalUserToFinCustomer($user) : ModelTransformer::userToFinCustomer($user);
        $std_customer = ModelTransformer::customerModelToStd($customer);
        if ($std_customer === false)
            return false;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/PostBusiness', $config)
            ->withData(['data' => $std_customer])->asJson()
            ->post();

        if (!is_null($curl_result->data)) {
            return $curl_result->data->result;
        } else {
            Log::warning('fin_manager.add_customer.std_false:' . $user->id . ':' . $is_legal . ':' . json_encode($user) . ":" . json_encode($curl_result));
            return false;
        }
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     * @param array $user_config
     */
    public function editCustomer($user, $is_legal, $user_config = []): bool {
        $customer = $is_legal ? ModelTransformer::legalUserToFinCustomer($user) :
            ModelTransformer::userToFinCustomer($user);
        $customer = ModelTransformer::userConfigToFinCustomer($customer, $user_config);
        $std_customer = ModelTransformer::customerModelToStd($customer);
        if ($std_customer === false) {
            Log::warning('fin_manager.edit_customer.std_false:' . $user->id . ':' . $is_legal . ':' . json_encode($user));
            return false;
        }
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/PutBusiness', $config)
            ->withData(['data' => $std_customer])->asJson()
            ->put();

        try {
            $result = $curl_result->data->result;
            if ($result == "true")
                return true;
            else {
                Log::warning('fin_manager.edit_customer.result_error:user_id:' . $user->id .
                    ':is_legal:' . $is_legal . json_encode($curl_result));
                return false;
            }
        } catch (Exception $exception) {
            Log::warning('fin_manager.edit_customer.data_parse_exception:' .
                $user->id . ':' . $is_legal . ':' . $exception->getMessage() . ":"
                . json_encode($curl_result));
            return false;
        }
    }

    /**
     * @return Product[]
     */
    public function getAllProducts() {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/GetItem', $config)
            ->asJson()
            ->get();
        $result = [];
        foreach ($curl_result->data as $stdProduct) {
            $product = ModelTransformer::productStdToModel($stdProduct);
            if ($product !== false)
                $result[] = $product;
        }
        return $result;
    }

    /**
     * @param string $code
     */
    public function getProduct($code): Product|bool {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/GetItem', $config)
            ->withData(['ItemCode' => $code])
            ->asJson()
            ->get();

        try {
            $stdProduct = $curl_result->data[0];
        } catch (Exception $e) {
            return false;
        }

        if ($stdProduct == null)
            return false;
        return ModelTransformer::productStdToModel($stdProduct);
    }

    /**
     * @param string $code
     */
    public function getProductCount($code): int|bool {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/GetStock', $config)
            ->withData(['ItemCode' => $code])
            ->asJson()
            ->get();

        try {
            $count = intval($curl_result->data[0]->stock);
        } catch (Exception $e) {
            return false;
        }
        return $count;
    }

    /**
     * @param Invoice $invoice
     */
    public function addPreInvoice($invoice): string|bool {
        $customerUser = $invoice->customer;

        /* TODO : here is a circular dependency,
         * Driver depends on Customer , Customer depends on User and finally user depends on Driver.
         */
        $updateFinManAddressResult = $customerUser->updateFinManAddress($invoice->is_legal,
            $invoice->customer_address . " - شماره تماس : " . $invoice->phone_number,
            $invoice->state_id);

        if ($updateFinManAddressResult === false) {
            Log::warning('fin_manager.add_pre_invoice: ' . $invoice->id . ':address_not_updated');
            return false;
        }
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $stdPreInvoice = ModelTransformer::invoiceModelToStd($invoice);
        if ($stdPreInvoice !== false) {
            $curl_result = ConnectionFactory::create('/serv/api/PostTransaction', $config)
                ->withData($stdPreInvoice)->asJson()
                ->post();
            $result = $curl_result->data ?? null;
            if ($result != null) {
                $result = json_encode($result);
                if ($result != 'null') {
                    return $result;
                }
            }
            Log::warning('fin_manager.add_pre_invoice.data_parse_exception: ' .
                json_encode($curl_result) . ' passed_data : ' . json_encode($stdPreInvoice));
        }
        Log::warning('fin_manager.add_pre_invoice.std_false:' . $invoice->id . ':' . json_encode($invoice));
        return false;
    }

    /**
     * @param string $fin_relation
     */
    public function deletePreInvoice($fin_relation): bool {
        $fin_relation = json_decode($fin_relation);

        if ($fin_relation == null)
            return false;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create(
            "/serv/api/DeleteTransaction?TransactionId={$fin_relation->transactionId}", $config)
            ->withData([
                "TransactionId" => $fin_relation->transactionId
            ])->asJson()
            ->delete();

        if (isset($curl_result->status) and $curl_result->status == "true") {
            return true;
        }
        Log::warning("fin_manager.delete_pre_invoice : " . json_encode($curl_result));
        return false;
    }

    /**
     * @param string $fin_relation
     */
    public function submitWarehousePermission($fin_relation): string|bool {
        $fin_relation = json_decode($fin_relation);

        if ($fin_relation == null)
            return false;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create('/serv/api/PostWarehousePermission', $config)
            ->withData([
                "data" => [
                    "TransactionId" => $fin_relation->transactionId,
                    "TransNumber" => $fin_relation->transNumber
                ]
            ])
            ->asJson()
            ->post();

        try {
            $result = $curl_result->data;
            if ($result != null)
                return json_encode($result);
            Log::warning("fin_manager.submit_warehouse_permission.error : curlResult null data");
            return false;
        } catch (Exception $exception) {
            Log::warning("fin_manager.submit_warehouse_permission.error :" . $exception->getMessage());
            return false;
        }
    }

    /**
     * @param string $warehouse_permission_data
     */
    public function checkExitTab($warehouse_permission_data): bool {
        $warehouse_permission_data = json_decode($warehouse_permission_data);

        if ($warehouse_permission_data == null)
            return false;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/serv/api/GetLinkedInfo?TransactionId={$warehouse_permission_data->transactionId}", $config)
            ->asJson()
            ->get();
        try {
            return isset($curl_result->data) and
                is_array($curl_result->data) and
                count($curl_result->data) > 0 and
                isset($curl_result->data[0]->transactionId);
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @param integer $standard_price
     */
    public function convertPrice($standard_price): int {
        return $standard_price;
    }
}
