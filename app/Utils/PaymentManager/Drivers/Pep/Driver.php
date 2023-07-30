<?php


namespace App\Utils\PaymentManager\Drivers\Pep;


use App\Enums\Invoice\PaymentStatus;
use App\Utils\PaymentManager\AbstractDriver;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Drivers\Pep\RSA\RSAKeyType;
use App\Utils\PaymentManager\Drivers\Pep\RSA\RSASignProcessor;
use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidParametersException;
use App\Utils\PaymentManager\Kernel;
use App\Utils\PaymentManager\Models\Form;
use App\Utils\PaymentManager\Models\FormAttribute;
use Exception;
use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;
use stdClass;

class Driver extends AbstractDriver
{

    const DRIVER_ID = 'pep';

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
     */
    public function getStatus($amount, $payment_id, $payment_data): int
    {
        $payment_data = json_decode($payment_data);
        if ($payment_data != null and isset($payment_data->result) and
            $payment_data->result == "true") {
            if (isset($payment_data->reject)) {
                if ($payment_data->reject === "true")
                    return PaymentStatus::CHARGED_BACK;
                else
                    return PaymentStatus::SUBMITTED;//NEEDS_CHECK
            } else {
                if (isset($payment_data->verify)) {
                    if ($payment_data->verify === "true")
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
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getPaymentId($payment_data): mixed
    {
        $payment_data = json_decode($payment_data);
        if (!isset($payment_data->iN) or $payment_data->iN == null or strlen($payment_data->iN) === 0) {
            throw new PaymentCallbackInvalidParametersException("There is no 'iN' in bank " .
                "'{$this->getId()}' payment data.");
        }
        return $payment_data->iN;
    }

    /**
     * @throws PaymentCallbackInvalidParametersException
     * @throws PaymentConnectionException
     */
    public function getTrackingCode($payment_data): mixed
    {
        $payment_data = json_decode($payment_data);

        if (!isset($payment_data->result))
            $payment_data = $this->getPaymentResult($payment_data);

        if (($payment_data != null) and isset($payment_data->traceNumber) and
            (strlen($payment_data->traceNumber) > 0))
            return $payment_data->traceNumber;

        throw new PaymentCallbackInvalidParametersException("There is no 'traceNumber' parameter in bank " .
            "'{$this->getId()}' result.");
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @throws PaymentConnectionException
     */
    public function isSuccessful($amount, $payment_id, $payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        if ($payment_data == null or !isset($payment_data->iN) or intval($payment_data->iN) !== $payment_id or
            !isset($payment_data->tref) or !isset($payment_data->iD)) {
            return false;
        }

        if (!isset($payment_data->result) or !isset($payment_data->invoiceNumber) or !isset($payment_data->amount)) {
            $payment_data = $this->getPaymentResult($payment_data);
        }

        return isset($payment_data->amount) and $payment_data->amount === $amount and
            isset($payment_data->result) and $payment_data->result === "true" and
            isset($payment_data->invoiceNumber) and $payment_data->invoiceNumber === $payment_id;
    }

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        if ($payment_data != null)
            return isset($payment_data->iN) and isset($payment_data->tref) and
                isset($payment_data->iD);

        return false;
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param array $extra_data
     * @throws PaymentInvalidParametersException
     * @throws PaymentDriverNotConfiguredException
     */
    public function initiatePayment($amount, $payment_id, $extra_data = []): string
    {
        $timestamp = date("Y/m/d H:i:s");
        $action = 1003;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $data = "#" . $config->mid . "#" . $config->tid .
            "#" . $payment_id . "#" . $timestamp . "#" . $amount . "#" . $this->getCallbackUrl() . "#" .
            $action . "#" . $timestamp . "#";

        $data = sha1($data, true);
        $processor = new RSASignProcessor(base_path($config->getFilePath()),
            RSAKeyType::XMLFile);
        $data = $processor->sign($data);
        $sign = base64_encode($data);

        $invoiceNumberInput = new FormAttribute("invoiceNumber", $payment_id);
        $invoiceDateInput = new FormAttribute("invoiceDate", "{$timestamp}");
        $amountInput = new FormAttribute("amount", "{$amount}");
        $terminalCodeInput = new FormAttribute("terminalCode", $config->tid);
        $merchantCodeInput = new FormAttribute("merchantCode", $config->mid);
        $redirectAddressInput = new FormAttribute("redirectAddress", $this->getCallbackUrl());
        $timeStampInput = new FormAttribute("timeStamp", "{$timestamp}");
        $actionInput = new FormAttribute("action", "{$action}");
        $signInput = new FormAttribute("sign", "{$sign}");
        $form = new Form("POST", $this->getRequestUrl("/gateway.aspx"), [
            $invoiceNumberInput, $invoiceDateInput, $amountInput, $terminalCodeInput,
            $merchantCodeInput, $redirectAddressInput, $timeStampInput, $actionInput, $signInput
        ]);

        request()->session()->put(Kernel::$sessionKey . ":form-object", $form);
        $payment_data = new PaymentData($sign);

        return json_encode($payment_data);
    }

    /**
     * @throws PaymentInvalidParametersException|PaymentConnectionException
     */
    public function verifyPayment($amount, $payment_id, $payment_data): string
    {
        $payment_data = json_decode($payment_data);
        $timestamp = date("Y/m/d H:i:s");
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $processor = new RSASignProcessor(base_path($config->getFilePath()),
            RSAKeyType::XMLFile);
        $data = "#" . $config->mid . "#" . $config->tid . "#" .
            $payment_id . "#" . $payment_data->iD . "#" . $amount . "#" . $timestamp . "#";

        $data = sha1($data, true);
        $data = $processor->sign($data);
        $sign = base64_encode($data);

        if (!isset($payment_data->result))
            $payment_data = $this->getPaymentResult($payment_data);

        try {

            $xmlResult = $this->createCurl("/VerifyPayment.aspx")->withData([
                'MerchantCode' => $config->mid,
                'TerminalCode' => $config->tid,
                'InvoiceNumber' => $payment_id,
                'InvoiceDate' => $payment_data->iD,
                'amount' => $amount,
                'TimeStamp' => $timestamp,
                'sign' => "{$sign}",
            ])->post();

            $stdResult = simplexml_load_string($xmlResult);
            $payment_data->verify = isset($stdResult->result) ? strtolower((string)$stdResult->result) : "false";
            $payment_data->verifyMessage = $stdResult->resultMessage;

            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()}" .
                " while verifying payment id '{$payment_id}'");
        }

    }

    /**
     * @throws PaymentConnectionException
     * @throws PaymentInvalidParametersException
     */
    public function rejectPayment($amount, $payment_id, $payment_data): string
    {
        $payment_data = json_decode($payment_data);
        $timestamp = date("Y/m/d H:i:s");
        $action = 1004;
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        $processor = new RSASignProcessor(base_path($config->getFilePath()),
            RSAKeyType::XMLFile);
        $data = "#" . $config->mid . "#" . $config->tid . "#" .
            $payment_id . "#" . $payment_data->iD . "#" . $amount . "#" . $action . "#" . $timestamp . "#";

        $data = sha1($data, true);
        $data = $processor->sign($data);
        $sign = base64_encode($data);

        try {

            $xmlResult = $this->createCurl("/doRefund.aspx")->withData([
                'MerchantCode' => $config->mid,
                'TerminalCode' => $config->tid,
                'InvoiceNumber' => $payment_id,
                'InvoiceDate' => $payment_data->iD,
                'amount' => $amount,
                'TimeStamp' => $timestamp,
                'action' => $action,
                'sign' => "{$sign}",
            ])->post();

            $stdResult = simplexml_load_string($xmlResult);
            $payment_data->reject = isset($stdResult->result) ? strtolower((string)$stdResult->result) : "false";
            $payment_data->rejectMessage = $stdResult->resultMessage;

            return json_encode($payment_data);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()}" .
                " while verifying payment id '{$payment_id}'");
        }
    }

    private function getRequestUrl($uri): string
    {
        $url = strpos($uri, '/') == 0 ? $uri : '/' . $uri;
        return Config::HOST . $url;
    }

    private function createCurl(string $uri): Builder
    {
        $address = $this->getRequestUrl($uri);
        return Curl::to($address);
    }

    /**
     * @throws PaymentConnectionException
     */
    private function getPaymentResult($payment_data): stdClass
    {
        if(! $payment_data instanceof stdClass)
            return new stdClass();

        if (!isset($payment_data->tref) or !isset($payment_data->iN))
            return $payment_data;

        try {
            $xmlResult = $this->createCurl("/CheckTransactionResult.aspx")->withData([
                "InvoiceUID" => $payment_data->tref,
            ])->post();
            $stdResult = simplexml_load_string($xmlResult);


            $payment_data->result = strtolower((string)$stdResult->result);
            $payment_data->amount = intval((string)$stdResult->amount);
            $payment_data->traceNumber = (string)$stdResult->traceNumber;
            $payment_data->referenceNumber = (string)$stdResult->referenceNumber;
            $payment_data->invoiceNumber = intval((string)$stdResult->invoiceNumber);

            return $payment_data;
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()}" .
                " while getting payment result for '{$payment_data->iN}'");
        }
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
