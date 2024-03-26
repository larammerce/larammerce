<?php

namespace App\Utils\PaymentManager\Drivers\Zarinpal;

use App\Enums\Invoice\PaymentStatus;
use App\Utils\PaymentManager\AbstractDriver;
use App\Utils\PaymentManager\ConfigProvider;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Kernel;
use App\Utils\PaymentManager\Models\Form;
use Exception;
use Ixudra\Curl\Builder;
use Ixudra\Curl\Facades\Curl;

class Driver extends AbstractDriver {
    const DRIVER_ID = 'zarinpal';
    const AUTHORIZATION_SESSION_NAME = "zarinpal_authorization";

    public function getId(): string {
        return static::DRIVER_ID;
    }

    public function getDefaultConfig(): Config {
        return new Config();
    }

    public function fixAmount($amount): int {
        return intval($amount);
    }

    public function getStatus($amount, $payment_id, $payment_data): int {
        $payment_data_decoded = json_decode($payment_data);

        if (isset($payment_data_decoded->verify)) {
            if ($payment_data_decoded->verify?->message == "Verified" or $payment_data_decoded->verify?->message == "Paid") {
                return PaymentStatus::CONFIRMED;
            }
        }
        return PaymentStatus::FAILED;
    }

    public function getPaymentId($payment_data): mixed {
        $payment_data_decoded = json_decode($payment_data);
        if (!isset($payment_data_decoded->Authority)) {
            return 0;
        }

        if (!request()->session()->has(static::AUTHORIZATION_SESSION_NAME)) {
            return 0;
        }

        $authorization_string = request()->session()->get(static::AUTHORIZATION_SESSION_NAME);
        if (!str_starts_with($authorization_string, $payment_data_decoded->Authority)) {
            return 0;
        }

        return str_replace("{$payment_data_decoded->Authority}:", "", $authorization_string);
    }

    public function getTrackingCode($payment_data): mixed {
        $payment_data_decoded = json_decode($payment_data);
        if (!isset($payment_data_decoded->verify))
            return "-";

        return $payment_data_decoded?->verify?->ref_id ?? "";
    }

    public function isSuccessful($amount, $payment_id, $payment_data): bool {
        $payment_data_decoded = json_decode($payment_data);
        return ($payment_data_decoded?->Status === "OK");
    }

    public function isCalledBack($payment_data): bool {
        $payment_data_decoded = json_decode($payment_data);
        return isset($payment_data_decoded->Status) and isset($payment_data_decoded->Authority);
    }

    public function initiatePayment($amount, $payment_id, $extra_data = []): string {
        try {
            /** @var Config $config */
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/pg/v4/payment/request.json")->withData(
                [
                    "amount" => $this->fixAmount($amount),
                    "callback_url" => $this->getCallbackUrl(),
                    "merchant_id" => $config->merchant_id,
                    "metadata" => [
                        "payment_id" => $payment_id,
                        "mobile" => $extra_data["phone_number"] ?? "",
                        "email" => $extra_data["email"] ?? ""
                    ],
                    "description" => "Payment {$payment_id}"
                ]
            )->asJson()->post();

            if ($result?->data?->code !== 100 or is_null($result?->data?->authority)) {
                throw new PaymentConnectionException("There was a problem defining new payment. " .
                    json_encode($result));
            }

            $form = new Form('GET', Config::FORM_ACTION . $result?->data?->authority, []);
            request()->session()->put(Kernel::$sessionKey . ":form-object", $form);
            request()->session()->put(static::AUTHORIZATION_SESSION_NAME, "{$result?->data?->authority}:{$payment_id}");

            return json_encode(new PaymentData($result?->data?->authority, $result?->data?->code, $result?->data?->message));
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message {$e->getMessage()}" .
                " while initiating payment id '{$payment_id}'");
        }
    }

    public function verifyPayment($amount, $payment_id, $payment_data): string|bool {
        try {
            $payment_data_decoded = json_decode($payment_data);
            if (!isset($payment_data_decoded->Authority))
                return false;

            /** @var Config $config */
            $config = ConfigProvider::getConfig(self::DRIVER_ID);
            $result = $this->createCurl("/pg/v4/payment/verify.json")->withData(
                [
                    "amount" => $this->fixAmount($amount),
                    "merchant_id" => $config->merchant_id,
                    "authority" => $payment_data_decoded->Authority
                ]
            )->asJson()->post();

            if (!isset($result?->data?->code) or !isset($result?->data?->message)) {
                throw new PaymentConnectionException("There was a problem defining at verify payment. " .
                    json_encode($result->ConfirmPaymentResult));
            }

            $payment_data_decoded->verify = $result->data;
            return json_encode($payment_data_decoded);
        } catch (Exception $e) {
            throw new PaymentConnectionException("There was unknown error with message '{$e->getMessage()}' " .
                "while verifying payment id '{$payment_id}'");
        }
    }

    public function rejectPayment($amount, $payment_id, $payment_data): string|bool {
        return false;
    }

    public function finalizePayment($amount, $payment_id, $payment_data): string {
        return $this->verifyPayment($amount, $payment_id, $payment_data);
    }

    private function createCurl(string $uri): Builder {
        $uri = strpos($uri, '/') == 0 ? $uri : '/' . $uri;
        $address = Config::API_HOST . $uri;
        return Curl::to($address);
    }
}
