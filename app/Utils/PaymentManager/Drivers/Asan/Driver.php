<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/10/19
 * Time: 9:27 AM
 */

namespace App\Utils\PaymentManager\Drivers\Asan;


use App\Enums\Invoice\PaymentStatus;
use App\Utils\PaymentManager\AbstractDriver;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use App\Utils\PaymentManager\Kernel;
use App\Utils\PaymentManager\Models\Form;
use App\Utils\PaymentManager\Models\FormAttribute;
use Exception;
use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class Driver extends AbstractDriver
{
    const DRIVER_ID = 'asan';

    public function getId(): string
    {
        return self::DRIVER_ID;
    }

    /**
     * @param float $amount
     */
    public function fixAmount($amount): int
    {
        return intval($amount);
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     */
    public function getStatus($amount, $payment_id, $payment_data): int
    {
        $payment_data = json_decode($payment_data);

        if (isset($payment_data->token))
            return PaymentStatus::PENDING;

        if (isset($payment_data->cancel)) {
            if ($payment_data->cancel->status === 200)
                return PaymentStatus::CHARGED_BACK;
            return PaymentStatus::SUBMITTED;//NEEDS check.
        }

        if (isset($payment_data->verify) and $payment_data->verify->status === 200)
            return PaymentStatus::CONFIRMED;

        return PaymentStatus::FAILED;
    }

    /**
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getPaymentId($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (!isset($payment_data->payment_id)) {
            throw new PaymentCallbackInvalidParametersException("There is no 'payment_id' in bank " .
                "'{$this->getId()}' payment data.");
        }
        return $payment_data->payment_id;
    }

    /**
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getTrackingCode($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (($payment_data != null) and isset($payment_data->PayGateTranID) and
            (strlen($payment_data->PayGateTranID) > 0))
            return $payment_data->PayGateTranID;
        throw new PaymentCallbackInvalidParametersException("There is no 'PayGateTranID' parameter in bank " .
            "'{$this->getId()}' result.");
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     */
    public function isSuccessful($amount, $payment_id, $payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/v1/TranResult")->withData([
                    "merchantConfigurationId" => intval($config->mcid),
                    "localInvoiceId" => $payment_id,
                ]
            )->asJson()->get();

            return isset($result->cardNumber) and isset($result->rrn) and isset($result->refID) and
                isset($result->amount) and isset($result->payGateTranID) and isset($result->salesOrderID) and
                intval($result->amount) === $amount and intval($result->salesOrderID) === $payment_id and
                $result->payGateTranID === $payment_data->PayGateTranID;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        return (isset($payment_data->payment_id) and isset($payment_data->ReturningParams) and
            isset($payment_data->PayGateTranID));
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param array $extra_data
     * @throws PaymentConnectionException
     */
    public function initiatePayment($amount, $payment_id, $extra_data = []): string
    {
        try {
            $local_date = $this->createCurl("/v1/Time")->asJson()->get();
            if (strlen($local_date) !== 15) {
                throw new PaymentConnectionException("There was a problem fetching local time from asan.");
            }

            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/v1/Token")->withData([
                    "serviceTypeId" => 1,
                    "merchantConfigurationId" => intval($config->mcid),
                    "localInvoiceId" => $payment_id,
                    "amountInRials" => $this->fixAmount($amount),
                    "localDate" => $local_date,
                    "additionalData" => "$payment_id",
                    "callbackURL" => $this->getCallbackUrl() . "?payment_id=${payment_id}"
                ]
            )->asJson()->post();

            if (!is_string($result) or strlen($result) !== 17) {
                throw new PaymentConnectionException("There was a problem defining new payment. " .
                    json_encode($result));
            }

            $token = new FormAttribute("RefId", $result);
            $mobile = key_exists("phone_number", $extra_data) ?
                new FormAttribute("mobileap", $extra_data["phone_number"]) : "";
            $form = new Form('POST', Config::FORM_ACTION,
                [$token, $mobile]);

            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);

            return json_encode(new PaymentData($result));

        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()}" .
                " while initiating payment id '{$payment_id}'");
        }
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @return string
     * @throws PaymentConnectionException
     */
    public function verifyPayment($amount, $payment_id, $payment_data): string
    {
        try {
            $payment_data = json_decode($payment_data);
            $pay_gate_tran_id = $payment_data->PayGateTranID ?? "";

            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/v1/Verify")->withData([
                "merchantConfigurationId" => intval($config->mcid),
                "payGateTranId" => $pay_gate_tran_id
            ])->asJson()->returnResponseObject()->post();

            $payment_data->verify = $result;

            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while verifying payment id '{$payment_id}'");
        }
    }

    /**
     * @throws PaymentConnectionException
     */
    public function rejectPayment($amount, $payment_id, $payment_data): string
    {
        try {
            $payment_data = json_decode($payment_data);
            $pay_gate_tran_id = isset($payment_data->PayGateTranID) ? $payment_data->PayGateTranID : "";

            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/v1/Cancel")->withData([
                "merchantConfigurationId" => intval($config->mcid),
                "payGateTranId" => $pay_gate_tran_id
            ])->asJson()->returnResponseObject()->post();

            $payment_data->cancel = $result;

            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while rejecting payment id '{$payment_id}'");
        }
    }

    /**
     * @throws PaymentDriverNotConfiguredException
     */
    private function createCurl(string $uri): Builder
    {
        $uri = strpos($uri, '/') == 0 ? $uri : '/' . $uri;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $username = $config->username;
        $password = $config->password;
        $address = Config::API_HOST . $uri;
        return Curl::to($address)->withHeaders([
            "usr: ${username}",
            "pwd: ${password}"
        ]);
    }

    /**
     * @param int $amount
     * @param int $payment_id
     * @param string $payment_data
     * @return string
     * @throws PaymentConnectionException
     */
    public function finalizePayment($amount, $payment_id, $payment_data): string
    {
        try {
            $payment_data = json_decode($payment_data);
            $pay_gate_tran_id = isset($payment_data->PayGateTranID) ? $payment_data->PayGateTranID : "";

            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/v1/Settlement")->withData([
                "merchantConfigurationId" => intval($config->mcid),
                "payGateTranId" => $pay_gate_tran_id
            ])->asJson()->returnResponseObject()->post();

            $payment_data->settlement = $result;

            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while settling payment id '{$payment_id}'");
        }
    }

    public function getDefaultConfig(): Config
    {
        return new Config();
    }
}
