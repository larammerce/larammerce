<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/10/19
 * Time: 9:27 AM
 */

namespace App\Utils\PaymentManager\Drivers\Pec;


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
use SoapClient;

class Driver extends AbstractDriver
{
    const DRIVER_ID = "pec";

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
        if ($this->isSuccessful($amount, $payment_id, $payment_data)) {
            $payment_data = json_decode($payment_data);
            if (isset($payment_data->reject) and isset($payment_data->reject->Status)) {
                if (intval($payment_data->reject->Status) === 0)
                    return PaymentStatus::CHARGED_BACK;
                else
                    return PaymentStatus::SUBMITTED;//NEEDS_CHECK
            } else {
                if (isset($payment_data->verify) and isset($payment_data->verify->Status)) {
                    if (intval($payment_data->verify->Status) === 0)
                        return PaymentStatus::CONFIRMED;
                    else {
                        return PaymentStatus::FAILED;//FAKE_PAYMENT
                    }
                } else
                    return PaymentStatus::CONFIRMED;
            }
        } else
            return PaymentStatus::FAILED;
    }

    /**
     * @param $payment_data
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getPaymentId($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (!isset($payment_data->OrderId) or $payment_data->OrderId == null or
            strlen($payment_data->OrderId) === 0) {
            throw new PaymentCallbackInvalidParametersException("There is no 'OrderId' in bank " .
                "'{$this->getId()}' payment data.");
        }
        return intval($payment_data->OrderId);
    }

    /**
     * @param $payment_data
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getTrackingCode($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (($payment_data != null) and isset($payment_data->RRN) and
            (strlen($payment_data->RRN) > 0))
            return $payment_data->RRN;
        throw new PaymentCallbackInvalidParametersException("There is no 'RRN' parameter in bank " .
            "'{$this->getId()}' result.");
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @throws PaymentDriverNotConfiguredException
     */
    public function isSuccessful($amount, $payment_id, $payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        if ($payment_data != null) {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            return isset($payment_data->Amount) and
                $this->fixAmount($amount) == intval(str_replace(",", "", $payment_data->Amount)) and
                $payment_data->TerminalNo == $config->tid and
                $payment_data->status === "0" and
                $payment_data->OrderId == $payment_id;
        }
        return false;
    }

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        return isset($payment_data->Status) and isset($payment_data->OrderId);
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
            $client = new SoapClient($this->getRequestURL("/NewIPGServices/Sale/SaleService.asmx", true));
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $client->SalePaymentRequest(
                [
                    "requestData" => [
                        "LoginAccount" => $config->pin,
                        "OrderId" => $payment_id,
                        "Amount" => $this->fixAmount($amount),
                        "CallBackUrl" => $this->getCallbackUrl(),
                    ]
                ]
            );
            if (!isset($result->SalePaymentRequestResult) or
                !isset($result->SalePaymentRequestResult->Status) or
                !isset($result->SalePaymentRequestResult->Token) or
                $result->SalePaymentRequestResult->Status !== 0) {
                throw new PaymentConnectionException("There was a problem defining new payment. " .
                    json_encode($result));
            }

            $form = new Form("POST", Config::FORM_ACTION,
                [new FormAttribute("Token", $result->SalePaymentRequestResult->Token)]);
            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);

            return json_encode(
                new PaymentData($result->SalePaymentRequestResult->Token, $result->SalePaymentRequestResult->Status)
            );

        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()}" .
                " while initiating payment id '{$payment_id}'");
        }
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @throws PaymentConnectionException
     */
    public function verifyPayment($amount, $payment_id, $payment_data): string
    {
        try {
            $payment_data = json_decode($payment_data);
            $client = new SoapClient($this->getRequestURL("/NewIPGServices/Confirm/ConfirmService.asmx",
                true));
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $client->ConfirmPayment(
                [
                    "requestData" => [
                        "LoginAccount" => $config->pin,
                        "Token" => $payment_data->Token
                    ]
                ]
            );
            if (!isset($result->ConfirmPaymentResult) or
                !isset($result->ConfirmPaymentResult->Status) or
                $result->ConfirmPaymentResult->Status !== 0) {
                throw new PaymentConnectionException("There was a problem defining at verify payment. " .
                    json_encode($result->ConfirmPaymentResult));
            }
            $payment_data->verify = $result->ConfirmPaymentResult;
            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while verifying payment id '{$payment_id}'");
        }
    }

    /**
     * @param $amount
     * @param $payment_id
     * @param $payment_data
     * @throws PaymentConnectionException
     */
    public function rejectPayment($amount, $payment_id, $payment_data): string
    {
        try {
            $payment_data = json_decode($payment_data);
            $client = new SoapClient($this->getRequestURL("/NewIPGServices/Reverse/ReversalService.asmx",
                true));
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $client->ReversalRequest(
                [
                    "requestData" => [
                        "LoginAccount" => $config->pin,
                        "Token" => $payment_data->token
                    ]
                ]
            );
            if (!isset($result->ReversalRequestResult) or
                !isset($result->ReversalRequestResult->Status) or
                $result->ReversalRequestResult->Status !== 0) {
                throw new PaymentConnectionException("There was a problem defining at reversal. " .
                    json_encode($result->ReversalRequestResult));
            }
            $payment_data->reject = $result->ReversalRequestResult;
            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while rejecting payment id '{$payment_id}'");
        }
    }

    private function getRequestURL(string $url, bool $wsdl = false): string
    {
        $wsdl = $wsdl ? '?WSDL' : '';
        $url = strpos($url, '/') == 0 ? $url : '/' . $url;
        return Config::HOST . $url . $wsdl;
    }

    /**
     * @param int $amount
     * @param int $payment_id
     * @param string $payment_data
     */
    public function finalizePayment($amount, $payment_id, $payment_data): string
    {
        //NOT SUPPORTED
        return $payment_data;
    }

    public function getDefaultConfig(): Config
    {
        return new Config();
    }
}
