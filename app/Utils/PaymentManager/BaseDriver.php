<?php
/**
 * Created by PhpStorm.
 * User: arash
 * Date: 4/24/18
 * Time: 6:46 PM
 */

namespace App\Utils\PaymentManager;

use App\Utils\PaymentManager\Exceptions\PaymentCallbackInvalidParametersException;
use App\Utils\PaymentManager\Exceptions\PaymentConnectionException;
use App\Utils\PaymentManager\Exceptions\PaymentInvalidParametersException;
use App\Utils\PaymentManager\Models\BasePaymentConfig;
use Illuminate\Contracts\Routing\UrlGenerator;

interface BaseDriver
{
    public function getId(): string;

    public function getName(): string;

    public function getLogo(): string;

    public function getDefaultConfig(): BasePaymentConfig;

    public function getCallbackUrl(): UrlGenerator|string;

    public function getCallbackUri(): string;

    /**
     * @param float $amount
     */
    public function fixAmount($amount): int;

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     */
    public function getStatus($amount, $payment_id, $payment_data): int;

    /**
     * @param string $payment_data
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getPaymentId($payment_data): mixed;

    /**
     * @param string $payment_data
     * @throws PaymentCallbackInvalidParametersException
     */
    public function getTrackingCode($payment_data): mixed;

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @throws PaymentConnectionException
     */
    public function isSuccessful($amount, $payment_id, $payment_data): bool;

    /**
     * @param string $payment_data
     */
    public function isCalledBack($payment_data): bool;

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param array $extra_data
     * @throws PaymentConnectionException
     * @throws PaymentInvalidParametersException
     */
    public function initiatePayment($amount, $payment_id, $extra_data = []):string;

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @throws PaymentConnectionException
     * @throws PaymentInvalidParametersException
     */
    public function verifyPayment($amount, $payment_id, $payment_data): string|bool;

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     * @throws PaymentConnectionException
     * @throws PaymentInvalidParametersException
     */
    public function rejectPayment($amount, $payment_id, $payment_data): string|bool;

    /**
     * @param integer $amount
     * @param integer $payment_id
     * @param string $payment_data
     */
    public function finalizePayment($amount, $payment_id, $payment_data): string;

}
