<?php

namespace App\Utils\PaymentManager\Drivers\BehPardakht;


use App\Enums\Invoice\PaymentStatus;
use App\Utils\PaymentManager\AbstractDriver;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentDriverNotConfiguredException;
use App\Utils\PaymentManager\Kernel;
use App\Utils\PaymentManager\Models\Form;
use App\Utils\PaymentManager\Models\FormAttribute;
use Carbon\Carbon;
use Exception;
use SoapClient;

class Driver extends AbstractDriver
{
    const DRIVER_ID = "behpardakht";

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

        if (isset($payment_data->result))
            return PaymentStatus::PENDING;

        if (isset($payment_data->reject)) {
            if ($payment_data->reject->return === "0")
                return PaymentStatus::CHARGED_BACK;
            return PaymentStatus::SUBMITTED; //NEEDS check.
        }
        if (isset($payment_data->verify) and $payment_data->verify->return === "0")
            return PaymentStatus::CONFIRMED;

        return PaymentStatus::FAILED;
    }

    /**
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getPaymentId($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (!isset($payment_data->SaleOrderId) or $payment_data->SaleOrderId == null or
            strlen($payment_data->SaleOrderId) === 0) {
            throw new PaymentCallbackInvalidParametersException("There is no 'SaleOrderId' in bank " .
                "'{$this->getId()}' payment data.");
        }
        return intval($payment_data->SaleOrderId);
    }

    /**
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getTrackingCode($payment_data): string
    {
        $payment_data = json_decode($payment_data);
        if (($payment_data != null) and isset($payment_data->SaleReferenceId) and
            (strlen($payment_data->SaleReferenceId) > 0))
            return $payment_data->SaleReferenceId;
        throw new PaymentCallbackInvalidParametersException("There is no 'saleReferenceId' parameter in bank " .
            "'{$this->getId()}' result.");
    }

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * * checker : in oky ta haddi
     */
    public function isSuccessful($amount, $payment_id, $payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        if ($payment_data != null)
            return isset($payment_data->SaleReferenceId) and
                $this->fixAmount($amount) == intval(str_replace(",", "", $payment_data->FinalAmount)) and
                $payment_data->ResCode === "0";
        return false;
    }

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool
    {
        $payment_data = json_decode($payment_data);
        return isset($payment_data->SaleOrderId) and isset($payment_data->RefId);
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
            $client = new SoapClient($this->getRequestURL("/pgwchannel/services/pgw",
                true));
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $client->bpPayRequest(
                [
                    'terminalId' => $config->tid,
                    'userName' => $config->username,
                    'userPassword' => $config->password,
                    'callBackUrl' => $this->getCallbackUrl(),
                    'amount' => $this->fixAmount($amount),
                    'localDate' => Carbon::now()->format('Ymd'),
                    'localTime' => Carbon::now()->format('Gis'),
                    'orderId' => $payment_id,
                    'additionalData' => '',
                    'payerId' => 0
                ]
            );
            $data = explode(',', $result->return);
            if ($data[0] != "0") {
                throw new PaymentConnectionException($this->translateStatus($data[0]), (int)$data[0]);
            }

            $ref_id = new FormAttribute("RefId", $data[1]);
            $mobile = key_exists("phone_number", $extra_data) ?
                new FormAttribute("mobileNo", $extra_data["phone_number"]) : "";
            $form = new Form('POST', $this->getRequestURL(Config::FORM_ACTION, false),
                [$ref_id, $mobile]);
            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);

            return json_encode(new PaymentData());

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
            if ($payment_data->ResCode != "0") {
                throw new PaymentConnectionException("There was error while verifying :'" . $this->translateStatus($payment_data->ResCode));
            }
            $data = $this->prepareVerificationData($payment_data);
            $client = new SoapClient($this->getRequestURL("/pgwchannel/services/pgw",
                true));
            $result = $client->bpVerifyRequest($data);

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
            $data = $this->prepareVerificationData($payment_data);
            $client = new SoapClient($this->getRequestURL("/pgwchannel/services/pgw",
                true));
            $result = $client->bpReversalRequest($data);
            $payment_data->reject = $result;
            return json_encode($payment_data);
        } catch (Exception $e) {
            $message = "There was error with message ";
            if (isset($result) and  isset($result->return))
                $message .= $this->translateStatus($result->return);
            throw new PaymentConnectionException( $message. "while rejecting payment id '{$payment_id}'");
        }
    }

    private function getRequestURL(string $url, bool $wsdl = false): string
    {
        $wsdl = $wsdl ? '?wsdl' : '';
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
        return $payment_data;
    }

    /**
     * Prepare data for payment verification
     * @param $payment_data
     * @throws PaymentDriverNotConfiguredException
     */
    protected function prepareVerificationData($payment_data): array
    {
        $config = ConfigProvider::getConfig(self::DRIVER_ID);
        return [
            'terminalId' => $config->tid,
            'userName' => $config->username,
            'userPassword' => $config->password,
            'orderId' => $payment_data->SaleOrderId,
            'saleOrderId' => $payment_data->SaleOrderId,
            'saleReferenceId' => $payment_data->SaleReferenceId,
        ];
    }

    /**
     * Convert status to a readable message.
     *
     * @param $status
     *
     * @return mixed|string
     */
    private function translateStatus($status)
    {
        $translations = [
            '0' => 'تراکنش با موفقیت انجام شد',
            '11' => 'شماره کارت نامعتبر است',
            '12' => 'موجودی کافی نیست',
            '13' => 'رمز نادرست است',
            '14' => 'تعداد دفعات وارد کردن رمز بیش از حد مجاز است',
            '15' => 'کارت نامعتبر است',
            '16' => 'دفعات برداشت وجه بیش از حد مجاز است',
            '17' => 'کاربر از انجام تراکنش منصرف شده است',
            '18' => 'تاریخ انقضای کارت گذشته است',
            '19' => 'مبلغ برداشت وجه بیش از حد مجاز است',
            '111' => 'صادر کننده کارت نامعتبر است',
            '112' => 'خطای سوییچ صادر کننده کارت',
            '113' => 'پاسخی از صادر کننده کارت دریافت نشد',
            '114' => 'دارنده کارت مجاز به انجام این تراکنش نیست',
            '21' => 'پذیرنده نامعتبر است',
            '23' => 'خطای امنیتی رخ داده است',
            '24' => 'اطلاعات کاربری پذیرنده نامعتبر است',
            '25' => 'مبلغ نامعتبر است',
            '31' => 'پاسخ نامعتبر است',
            '32' => 'فرمت اطلاعات وارد شده صحیح نمی‌باشد',
            '33' => 'حساب نامعتبر است',
            '34' => 'خطای سیستمی',
            '35' => 'تاریخ نامعتبر است',
            '41' => 'شماره درخواست تکراری است',
            '42' => 'تراکنش Sale یافت نشد',
            '43' => 'قبلا درخواست Verify داده شده است',
            '44' => 'درخواست Verify یافت نشد',
            '45' => 'تراکنش Settle شده است',
            '46' => 'تراکنش Settle نشده است',
            '47' => 'تراکنش Settle یافت نشد',
            '48' => 'تراکنش Reverse شده است',
            '412' => 'شناسه قبض نادرست است',
            '413' => 'شناسه پرداخت نادرست است',
            '414' => 'سازمان صادر کننده قبض نامعتبر است',
            '415' => 'زمان جلسه کاری به پایان رسیده است',
            '416' => 'خطا در ثبت اطلاعات',
            '417' => 'شناسه پرداخت کننده نامعتبر است',
            '418' => 'اشکال در تعریف اطلاعات مشتری',
            '419' => 'تعداد دفعات ورود اطلاعات از حد مجاز گذشته است',
            '421' => 'IP نامعتبر است',
            '51' => 'تراکنش تکراری است',
            '54' => 'تراکنش مرجع موجود نیست',
            '55' => 'تراکنش نامعتبر است',
            '61' => 'خطا در واریز',
            '62' => 'مسیر بازگشت به سایت در دامنه ثبت شده برای پذیرنده قرار ندارد',
            '98' => 'سقف استفاده از رمز ایستا به پایان رسیده است'
        ];

        $unknownError = 'خطای ناشناخته رخ داده است.';

        return array_key_exists($status, $translations) ? $translations[$status] : $unknownError;
    }

    public function getDefaultConfig(): Config
    {
        return new Config();
    }
}
