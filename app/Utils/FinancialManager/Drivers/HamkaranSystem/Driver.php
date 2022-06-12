<?php namespace App\Utils\FinancialManager\Drivers\HamkaranSystem;


use App\Models\Invoice;
use App\Models\User;
use App\Utils\FinancialManager\BaseDriver;
use App\Utils\FinancialManager\ConfigProvider;
use App\Utils\FinancialManager\Exceptions\InvalidFinRelationException;
use App\Utils\FinancialManager\Models\Customer;
use App\Utils\FinancialManager\Models\Product;
use App\Utils\FinancialManager\Drivers\Local\Driver as LocalDriver;
use Illuminate\Support\Facades\Log;

class Driver implements BaseDriver
{
    const DRIVER_ID = "hamkaran";

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
        //There is no get all customers web service in hamkaran system.
        return [];
    }

    /**
     * @param $phone_number
     */
    public function getCustomerByPhone($phone_number): Customer|bool
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/System/BusinessRuleEngine/Service.svc/Call", $config)
            ->withData([
                "Name" => "GetCustomerWithInfo",
                "Parameters" => "{\"ID\": \"{$phone_number}\"}"
            ])
            ->asJson()
            ->post();
        $curl_result = json_decode($curl_result);
        if (isset($curl_result->CustomerID) and $curl_result->CustomerID !== 0) {
            return ModelTransformer::customerStdToModel($curl_result);
        }
        return false;
    }

    /**
     * @param string $relation
     */
    public function getCustomerByRelation($relation): Customer|bool
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/System/BusinessRuleEngine/Service.svc/Call", $config)
            ->withData([
                "Name" => "GetCustomerWithInfo",
                "Parameters" => "{\"ID\": \"{$relation}\"}"
            ])
            ->asJson()
            ->post();
        $curl_result = json_decode($curl_result);
        if (isset($curl_result->CustomerID) and $curl_result->CustomerID !== 0) {
            return ModelTransformer::customerStdToModel($curl_result);
        }
        return false;
    }

    /**
     * /General/PartyManagement/Services/PartyService.svc/GenerateParty
     *
     * @param User $user
     * @param boolean $is_legal
     */
    public function addCustomer($user, $is_legal): string|bool
    {
        $customer = $is_legal ? ModelTransformer::legalUserToFinCustomer($user) : ModelTransformer::userToFinCustomer($user);
        $std_customer = ModelTransformer::customerModelToStd($customer);
        if ($std_customer === false) {
            Log::error("finman.hamkaran.add-customer.transform-model-failed");
            return false;
        }
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/General/PartyManagement/Services/PartyService.svc/GenerateParty", $config)
            ->withData([$std_customer])->asJson()
            ->post();
        if ($curl_result !== null and is_array($curl_result) and count($curl_result) > 0 and
            isset($curl_result[0]->ID) and $curl_result[0]->ID !== 0) {
            $customer = $this->getCustomerByRelation($curl_result[0]->ID);
            if ($customer !== false)
                return $customer->id;
        } else {
            $customer = $this->getCustomerByPhone($user->customerUser->main_phone);
            if ($customer !== false)
                return $customer->id;
            else {
                $customer = $this->getCustomerByRelation($user->customerUser->national_code);
                if ($customer !== false)
                    return $customer->id;
            }
        }
        return false;
    }

    /**
     * @param User $user
     * @param boolean $is_legal
     * @param array $config
     */
    public function editCustomer($user, $is_legal, $config = []): bool
    {
        //There is no edit customer in hamkaran webservices.
        return true;
    }

    /**
     * @return Product[]
     */
    public function getAllProducts()
    {
        //There is no get all products web service in hamkaran system.
        return [];
    }

    /**
     * @param string $code
     */
    public function getProduct($code): Product|bool
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/System/BusinessRuleEngine/Service.svc/Call", $config)
            ->withData([
                "Name" => "GetProductDetail",
                "Parameters" => "{\"ProductID\": \"{$code}\"}"
            ])
            ->asJson()
            ->post();
        $curl_result = json_decode($curl_result);
        return ModelTransformer::productStdToModel($curl_result);
    }

    /**
     * @param string $code
     */
    public function getProductCount($code): int|bool
    {
        return false;
    }

    /**
     * @param Invoice $invoice
     */
    public function addPreInvoice($invoice): string|bool
    {
        foreach ($invoice->rows as $row) {
            $extras = array_filter(json_decode($row->product->extra_properties),
                function ($extra) {
                    return strtoupper($extra->key) == "ID";
                }
            );
            if (count(is_countable($extras)?$extras :[]) > 0) {
                $row->hamkaran_product_id = array_pop($extras)->value;
            } else {
                $hamkaran_product = $this->getProduct($row->product->code);
                if ($hamkaran_product !== false)
                    $row->hamkaran_product_id = $hamkaran_product->id;
                else
                    return false;
            }
        }
        $std_invoice = ModelTransformer::invoiceModelToStd($invoice);
        if ($std_invoice != false) {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $curl_result = ConnectionFactory::create("/Sales/OrderManagement/Services/OrderManagementService.svc/PlaceQuotation",
                $config)
                ->withData($std_invoice)->asJson()
                ->post();
            if ($curl_result != null) {
                if ($config->is_manual_stock) {
                    $local_driver = new LocalDriver();
                    $local_driver->addPreInvoice($invoice);
                }
                return $curl_result;
            }
            Log::error("fin_manager.add_pre_invoice.data_parse_exception: " .
                json_encode($curl_result) . " passed_data : " . json_encode($std_invoice));
        }
        Log::error("fin_manager.add_pre_invoice.std_false:" . $invoice->id . ":" . json_encode($invoice));
        return false;
    }

    /**
     * @param string $fin_relation
     * @throws InvalidFinRelationException
     */
    public function deletePreInvoice($fin_relation): bool
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/Sales/OrderManagement/Services/OrderManagementService.svc/ChangeQuotationState/{$fin_relation}/Canceled",
        $config)
            ->asJson()
            ->returnResponseObject()
            ->post();
        if ($curl_result->status == 200) {
            if ($config->is_manual_stock) {
                $local_driver = new LocalDriver();
                $local_driver->deletePreInvoice($fin_relation);
            }
            return true;
        }
        return false;
    }

    /**
     * @param string $fin_relation
     */
    public function submitWarehousePermission($fin_relation): string|bool
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/Sales/OrderManagement/Services/OrderManagementService.svc/ChangeQuotationState/{$fin_relation}/Confirmed",
            $config)
            ->asJson()
            ->returnResponseObject()
            ->post();
        return $curl_result->status == 200 ? $fin_relation : false;
    }

    /**
     * @param string $fin_relation
     */
    public function checkExitTab($fin_relation): bool
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $curl_result = ConnectionFactory::create("/System/BusinessRuleEngine/Service.svc/Call", $config)
            ->withData([
                "Name" => "CheckExitTab",
                "Parameters" => "{\"QutationID\": {$fin_relation}}"
            ])
            ->asJson()
            ->post();
        $curl_result = json_decode($curl_result);

        if (isset($curl_result->IsExit))
            return $curl_result->IsExit;
        return false;
    }

    /**
     * @param integer $standard_price
     */
    public function convertPrice($standard_price): int
    {
        return $standard_price;
    }
}
