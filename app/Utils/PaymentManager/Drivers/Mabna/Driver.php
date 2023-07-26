<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/10/19
 * Time: 9:27 AM
 */

namespace App\Utils\PaymentManager\Drivers\Mabna;


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
    const DRIVER_ID = 'mabna';

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
            if (isset($payment_data->reject) and isset($payment_data->reject->ReturnId)) {
                if (intval($payment_data->reject->ReturnId) === $this->fixAmount($amount))
                    return PaymentStatus::CHARGED_BACK;
                else
                    return PaymentStatus::SUBMITTED;//NEEDS_CHECK
            } else {
                if (isset($payment_data->verify) and isset($payment_data->verify->ReturnId)) {
                    if (intval($payment_data->verify->ReturnId) === $this->fixAmount($amount))
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
        if (!isset($payment_data->invoiceid) or $payment_data->invoiceid == null or
            strlen($payment_data->invoiceid) === 0) {
            throw new PaymentCallbackInvalidParametersException("There is no 'invoiceid' in bank " .
                "'{$this->getId()}' payment data.");
        }
        return intval($payment_data->invoiceid);
    }

    /**
     * @param $payment_data
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getTrackingCode($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (($payment_data != null) and isset($payment_data->tracenumber) and
            (strlen($payment_data->tracenumber) > 0))
            return $payment_data->tracenumber;
        throw new PaymentCallbackInvalidParametersException("There is no 'tracenumber' parameter in bank " .
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
            return isset($payment_data->amount) and
                $this->fixAmount($amount) == intval($payment_data->amount) and
                $payment_data->terminalid == $config->tid and
                $payment_data->respcode == "0" and
                $payment_data->invoiceid == $payment_id;
        }
        return false;
    }

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        return isset($payment_data->respcode) and isset($payment_data->invoiceid);
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param array $extra_data
     * @return string
     * @throws PaymentConnectionException
     */
    public function initiatePayment($amount, $payment_id, $extra_data=[]): string
    {
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);

            $result = $this->createCurl("/V1/PeymentApi/GetToken")->withData(
                [
                    "Amount" => $this->fixAmount($amount),
                    "callbackURL" => $this->getCallbackUrl(),
                    "invoiceID" => $payment_id,
                    "terminalID" => $config->tid,
                    "payload" => ""
                ]
            )->asJson()->post();
            if (!isset($result->Status) or $result->Status !== 0 or !isset($result->Accesstoken)) {
                throw new PaymentConnectionException("There was a problem defining new payment. " .
                    json_encode($result));
            }

            $token = new FormAttribute('token', $result->Accesstoken);
            $terminalId = new FormAttribute('TerminalID', $config->tid);
            $redirectURL = new FormAttribute('RedirectURL', $this->getCallbackUrl());
            $form = new Form('POST', Config::FORM_ACTION,
                [$token, $terminalId, $redirectURL]);

            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);

            return json_encode(new PaymentData($result->Accesstoken, $result->Status));

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
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/V1/PeymentApi/Advice")->withData(
                [
                    "digitalreceipt" => $payment_data->digitalreceipt,
                    "terminalid" => $config->tid,
                ]
            )->asJson()->post();

            $payment_data->verify = $result;

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
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/V1/PeymentApi/RollBack")->withData(
                [
                    "digitalreceipt" => $payment_data->digitalreceipt,
                    "terminalid" => $config->tid,
                ]
            )->asJson()->post();

            $payment_data->reject = $result;

            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while rejecting payment id '{$payment_id}'");
        }
    }

    private function createCurl(string $uri): Builder
    {
        $uri = strpos($uri, '/') == 0 ? $uri : '/' . $uri;
        $address = Config::API_HOST . $uri;
        return Curl::to($address);
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

    public function getDefaultConfig():Config
    {
        return new Config();
    }
}
