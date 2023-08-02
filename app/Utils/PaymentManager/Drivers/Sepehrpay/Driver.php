<?php
/**
 * Created by PhpStorm.
 * User: a.morteza
 * Date: 19/08/19
 * Time: 11:49
 */
namespace App\Utils\PaymentManager\Drivers\Sepehrpay;

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
    const DRIVER_ID = 'sepehrpay';

    /**
     * @return string
     */
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
     * @return int
     */
    public function getStatus($amount, $payment_id, $payment_data): int
    {
        $payment_data = json_decode($payment_data);
        if ($payment_data != null and
            isset($payment_data->respcode) and
            $payment_data->respcode == "0" and
            isset($payment_data->digitalreceipt) and
            strlen($payment_data->digitalreceipt) > 0) {
            if (isset($payment_data->reject)) {
                if (isset($payment_data->reject->Status) and
                    $payment_data->reject->Status == "Ok")
                    return PaymentStatus::CHARGED_BACK;
                else
                    return PaymentStatus::SUBMITTED;//NEEDS_CHECK
            } else {
                if (isset($payment_data->verify)) {
                    if (isset($payment_data->verify->Status) and
                        $payment_data->verify->Status == "Ok" and
                        isset($payment_data->verify->ReturnId) and
                        $payment_data->verify->ReturnId == "{$amount}"
                    )
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
                $payment_data->invoiceid == $payment_id and
                isset($payment_data->digitalreceipt) and
                strlen($payment_data->digitalreceipt) > 0;
        }
        return false;
    }

    /**
     * @param string $payment_data
     * @return bool
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        return isset($payment_data->Status) and isset($payment_data->ReturnId);
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param array $extra_data
     * @throws PaymentConnectionException
     */
    public function initiatePayment($amount, $payment_id, $extra_data=[]): string
    {
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $terminalIDInput = new FormAttribute("TerminalID", $config->tid);
            $amountInput = new FormAttribute("Amount", "{$amount}");
            $callbackURLInput = new FormAttribute("callbackURL", $this->getCallbackUrl());
            $invoiceIDInput = new FormAttribute("InvoiceID", "{$payment_id}");
            $form = new Form("POST", Config::FORM_ACTION, [
                $terminalIDInput, $amountInput, $callbackURLInput, $invoiceIDInput
            ]);
            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);
            return json_encode(New PaymentData());
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
    public function verifyPayment($amount, $payment_id, $payment_data): bool|string
    {
        try {
            $payment_data = json_decode($payment_data);
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/V1/PeymentApi/Advice")->withData(
                [
                    "digitalreceipt" => $payment_data->digitalreceipt,
                    "Tid" => $config->tid,
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
     * @return string
     * @throws PaymentConnectionException
     */
    public function rejectPayment($amount, $payment_id, $payment_data): string
    {
        try {
            $payment_data = json_decode($payment_data);
            $digitalReceipt = $payment_data->digitalreceipt;
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $terminalId = $config->tid;
            $dataQuery ='digitalreceipt='.$digitalReceipt.'&Tid='.$terminalId;
            $RollbackAddress = Config::API_HOST . "/V1/PeymentApi/Rollback";
            $result = $this->makeHttpRequest('POST', $dataQuery, $RollbackAddress);
            $payment_data->reject = $result;
            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while rejecting payment id '{$payment_id}'");
        }
    }

    /**
     * @param string $uri
     * @return Builder
     */
    private function createCurl(string $uri): Builder
    {
        $uri = strpos($uri, '/') == 0 ? $uri : '/' . $uri;
        $address = Config::API_HOST . $uri;
        return Curl::to($address);
    }

    private function makeHttpRequest($_Method, $_Data, $_Address): bool|string
    {
        $curl= curl_init();
        curl_setopt($curl, CURLOPT_URL, $_Address);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $_Method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $_Data);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * @param int $amount
     * @param int $payment_id
     * @param string $payment_data
     * @return string
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
