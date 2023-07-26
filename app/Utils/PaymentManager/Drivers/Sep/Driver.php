<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/24/18
 * Time: 6:46 PM
 */

namespace App\Utils\PaymentManager\Drivers\Sep;


use App\Enums\Invoice\PaymentStatus;
use App\Utils\PaymentManager\AbstractDriver;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use App\Utils\PaymentManager\Kernel;
use App\Utils\PaymentManager\Models\Form;
use App\Utils\PaymentManager\Models\FormAttribute;
use Mockery\Exception;
use SoapClient;
use SoapFault;

class Driver extends AbstractDriver
{
    const DRIVER_ID = 'sep';

    public function getId(): string
    {
        return self::DRIVER_ID;
    }

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
        if ($payment_data != null and isset($payment_data->StateCode) and
            $payment_data->StateCode == "0") {
            if (isset($payment_data->reject)) {
                if (intval($payment_data->reject) === 1)
                    return PaymentStatus::CHARGED_BACK;
                else
                    return PaymentStatus::SUBMITTED;//NEEDS_CHECK
            } else {
                if (isset($payment_data->verify)) {
                    if (intval($payment_data->verify) === $this->fixAmount($amount))
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
    public function getPaymentId($payment_data): mixed
    {
        $payment_data = json_decode($payment_data);
        if (!isset($payment_data->ResNum) or $payment_data->ResNum == null or strlen($payment_data->ResNum) === 0) {
            throw new PaymentCallbackInvalidParametersException("There is no 'ResNum' in bank " .
                "'{$this->getId()}' payment data.");
        }
        return $payment_data->ResNum;
    }

    /**
     * @param $payment_data
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getTrackingCode($payment_data): mixed
    {
        $payment_data = json_decode($payment_data);
        if (($payment_data != null) and isset($payment_data->TRACENO) and
            (strlen($payment_data->TRACENO) > 0))
            return $payment_data->TRACENO;
        throw new PaymentCallbackInvalidParametersException("There is no 'TRACENO' parameter in bank " .
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
                $this->fixAmount($amount) == intval($payment_data->Amount) and
                $payment_data->MID == $config->mid and
                $payment_data->State == "OK" and
                $payment_data->StateCode == "0" and
                $payment_data->ResNum == $payment_id;
        }
        return false;
    }

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        return isset($payment_data->State) and isset($payment_data->ResNum);
    }

    /**
     * @param int $amount
     * @param int $payment_id
     * @param array $extra_data
     * @throws PaymentConnectionException
     */
    public function initiatePayment($amount, $payment_id, $extra_data=[]): string
    {
        try {
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $client = new SoapClient($this->getRequestURL(Config::INIT_PAYMENT,
                true));
            $result = $client->__soapCall("RequestToken",
                [
                    $config->mid,
                    "$payment_id",
                    $this->fixAmount($amount)
                ]
            );

            $tmpResult = intval($result);
            if($tmpResult < 0){
                throw new PaymentConnectionException("There was a problem defining new payment.", $tmpResult);
            }

            $token = new FormAttribute('Token', $result);
            $redirectURL = new FormAttribute('RedirectURL', $this->getCallbackUrl());
            $form = new Form('POST', $this->getRequestURL(Config::FORM),
                [$token, $redirectURL]);

            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);

            return json_encode(new PaymentData($result));
        } catch (SoapFault $e) {
            throw new PaymentConnectionException("There was soap exception while initiating payment id " .
                "'{$payment_id}'");
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
            $client = new SoapClient($this->getRequestURL(Config::INIT_REFERENCE,
                true));
            $payment_data = json_decode($payment_data);
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $payment_data->verify = $client->__soapCall("verifyTransaction",
                [
                    $payment_data->RefNum,
                    $config->mid
                ]);

            return json_encode($payment_data);
        } catch (SoapFault $e) {
            throw new PaymentConnectionException("There was soap exception while verifying payment id " .
                "'{$payment_id}'");
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()} " .
                "while verifying payment id '{$payment_id}'");
        }
    }

    /**
     * @param $amount
     * @param $payment_id
     * @param $payment_data
     * @throws PaymentConnectionException|PaymentDriverNotConfiguredException
     */
    public function rejectPayment($amount, $payment_id, $payment_data): string
    {
        try {
            $client = new SoapClient($this->getRequestURL(Config::INIT_REFERENCE,
                true));
            $payment_data = json_decode($payment_data);
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $payment_data->reject = $client->__soapCall("reverseTransaction",
                [
                    $payment_data->RefNum,
                    $config->mid,
                    $config->username,
                    $config->password
                ]
            );

            return json_encode($payment_data);
        } catch (SoapFault $e) {
            throw new PaymentConnectionException("There was soap exception while rejecting payment with ref " .
                "'{$payment_data->RefNum}'");
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()} " .
                "while rejecting payment with ref '{$payment_data->RefNum}'");
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
